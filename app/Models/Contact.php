<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * A person the office deals with. Enquiries and orders attach to a contact by email address,
 * so one person's whole history sits in one place.
 */
class Contact extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'organisation', 'country', 'source'];

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }

    /** Find the contact with this email address, or create one. Empty details are filled in; existing ones are kept. */
    public static function sync(string $name, ?string $email, ?string $phone, ?string $organisation, string $source): ?self
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return null;
        }

        $contact = static::firstOrCreate(['email' => $email], ['name' => $name, 'phone' => $phone, 'organisation' => $organisation, 'source' => $source]);

        $missing = array_filter([
            'phone' => $contact->phone ? null : $phone,
            'organisation' => $contact->organisation ? null : $organisation,
        ]);
        if ($missing) {
            $contact->update($missing);
        }

        return $contact;
    }
}
