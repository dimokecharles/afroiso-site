<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $slug = 'iso-9001-quality-management';

    private string $description = <<<'TEXT'
ISO 9001 is the most widely used standard for quality management in the world. This course takes you through the 2026 edition one requirement at a time and shows how each requirement works in a real organisation.

## Who the course is for
- People who are new to ISO 9001 and want to understand the standard
- Quality managers and coordinators who run a quality management system or are building one
- Owners and managers who are preparing their organisation for certification
- Staff in certified organisations that are moving from ISO 9001:2015 to the 2026 edition

## What you will learn
- What a quality management system is and the principles behind it
- How to set out the context of the organisation, its interested parties and the scope of the system
- What the standard expects of leadership, including the quality policy and responsibilities
- How to plan for risks and opportunities, quality objectives and changes
- How to manage resources, competence, awareness, communication and documented information
- How to plan and control operations, from customer requirements to delivery
- How to monitor and measure performance, including internal audit and management review
- How to deal with nonconformity, take corrective action and keep improving
- What has changed since the 2015 edition
- How to prepare an organisation for a certification audit

## How the course works
The course is delivered by e-learning, so you can study from wherever you are. Classes begin on the start date shown on this page.

## How to enrol
Press "Enrol on this course" and send us your details. Our office will reply with the next steps and an invoice.
TEXT;

    /** Fills in the ISO 9001 course page. Only applies while the description is still empty, so admin edits are kept. */
    public function up(): void
    {
        DB::table('courses')->where('slug', $this->slug)
            ->where(fn ($query) => $query->whereNull('description')->orWhere('description', ''))
            ->update(['description' => $this->description]);
    }

    public function down(): void
    {
        DB::table('courses')->where('slug', $this->slug)->where('description', $this->description)->update(['description' => null]);
    }
};
