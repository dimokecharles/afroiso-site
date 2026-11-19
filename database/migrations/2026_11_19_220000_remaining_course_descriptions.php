<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Course page wording, by slug. Only applied while a course's description is still empty, so admin edits are kept. */
    private function descriptions(): array
    {
        $descriptions = [];

        $descriptions['iso-14001-environmental-management'] = <<<'TEXT'
ISO 14001 is the international standard for environmental management systems. This course takes you through the 2015 edition one requirement at a time and shows how an organisation uses it to control its effect on the environment and meet its legal duties.

## Who the course is for
- People who are new to ISO 14001 and want to understand the standard
- Environmental and SHEQ officers who run an environmental management system or are building one
- Owners and managers who are preparing their organisation for certification
- Staff whose work affects the environment, such as operations, maintenance and waste handling

## What you will learn
- What an environmental management system is and what it is meant to achieve
- How to set out the context of the organisation, its interested parties and the scope of the system
- What the standard expects of leadership, including the environmental policy and responsibilities
- How to identify environmental aspects and impacts, with a life cycle perspective
- How to identify compliance obligations and check that they are being met
- How to set environmental objectives and plan how to reach them
- How to control operations and prepare for emergencies
- How to monitor and measure performance, including internal audit and management review
- How to deal with nonconformity, take corrective action and keep improving
- How to prepare an organisation for a certification audit

## How the course works
The course is delivered by e-learning, so you can study from wherever you are. Classes begin on the start date shown on this page.

## How to enrol
Press "Enrol on this course" and send us your details. Our office will reply with the next steps and an invoice.
TEXT;

        $descriptions['iso-45001-occupational-health-and-safety'] = <<<'TEXT'
ISO 45001 is the international standard for occupational health and safety management systems. This course takes you through the 2018 edition one requirement at a time and shows how an organisation uses it to prevent injury and ill health among the people who work for it.

## Who the course is for
- People who are new to ISO 45001 and want to understand the standard
- Health and safety officers and SHEQ practitioners who run a health and safety management system or are building one
- Owners, managers and supervisors who are responsible for the safety of workers
- Health and safety representatives and committee members
- Staff in organisations that are preparing for certification

## What you will learn
- What an occupational health and safety management system is and what it is meant to achieve
- How to set out the context of the organisation, the needs of workers and the scope of the system
- What the standard expects of leadership, and how workers are consulted and take part
- How to identify hazards and assess health and safety risks and opportunities
- How to identify legal requirements and check that they are being met
- How to set health and safety objectives and plan how to reach them
- How to control work using the hierarchy of controls, and how to manage change, contractors and purchasing
- How to prepare for and respond to emergencies
- How to investigate incidents, deal with nonconformity and take corrective action
- How to monitor performance, including internal audit and management review
- How to prepare an organisation for a certification audit

## How the course works
The course is delivered by e-learning, so you can study from wherever you are. Classes begin on the start date shown on this page.

## How to enrol
Press "Enrol on this course" and send us your details. Our office will reply with the next steps and an invoice.
TEXT;

        $descriptions['iso-iec-27001-information-security'] = <<<'TEXT'
ISO/IEC 27001 is the international standard for information security management systems. This course takes you through the 2022 edition one requirement at a time and shows how an organisation uses it to protect the information it holds, on paper and in its systems.

## Who the course is for
- People who are new to ISO/IEC 27001 and want to understand the standard
- IT, security, risk and compliance staff who run an information security management system or are building one
- Owners and managers who are preparing their organisation for certification
- Staff who handle sensitive information, such as customer, financial or personal records

## What you will learn
- What an information security management system is, and what confidentiality, integrity and availability mean
- How to set out the context of the organisation, its interested parties and the scope of the system
- What the standard expects of leadership, including the information security policy and responsibilities
- How to carry out an information security risk assessment
- How to choose risk treatment and write a Statement of Applicability
- What the Annex A controls cover: organisational, people, physical and technological
- How to set information security objectives and plan how to reach them
- How to manage competence, awareness and documented information
- How to monitor and measure performance, including internal audit and management review
- How to deal with nonconformity, take corrective action and keep improving
- How to prepare an organisation for a certification audit

## How the course works
The course is delivered by e-learning, so you can study from wherever you are. Classes begin on the start date shown on this page.

## How to enrol
Press "Enrol on this course" and send us your details. Our office will reply with the next steps and an invoice.
TEXT;

        $descriptions['food-safety-and-hygiene'] = <<<'TEXT'
This course covers the hygiene requirements for food premises and for the transport of food under Regulation R638 in South Africa and Statutory Instrument 5 of 2015 in Zimbabwe. It explains what the regulations require and how to meet those requirements in day-to-day work.

## Who the course is for
- Food handlers in kitchens, restaurants, shops, factories and catering businesses
- Owners, managers and supervisors who are in charge of food premises
- People who transport or deliver food
- Staff responsible for hygiene, cleaning and quality in a food business

## What you will learn
- Why food safety matters and how food becomes unsafe
- What the regulations require of food premises and of the people in charge of them
- Personal hygiene and the duties of a food handler
- How to prevent contamination and cross-contamination
- Temperature control for storing, preparing, displaying and serving food
- Cleaning and sanitising of premises, equipment and utensils
- Pest control and waste handling
- Hygiene requirements for transporting food
- What records to keep and what an inspection looks at

## How the course works
The course is delivered by e-learning, so you can study from wherever you are. Classes begin on the start date shown on this page.

## How to enrol
Press "Enrol on this course" and send us your details. Our office will reply with the next steps and an invoice.
TEXT;

        $descriptions['internal-auditor'] = <<<'TEXT'
This course teaches you how to carry out internal audits of a management system. The method is the same whichever standard your organisation follows, including quality, environmental, health and safety, and information security.

## Who the course is for
- Staff who have been asked to audit their own organisation's management system
- Quality, environmental, health and safety, and information security officers
- Managers who want to understand what an internal audit looks at and how to use the results
- People who want to begin a career in auditing

## What you will learn
- What an internal audit is for and the principles an auditor works by
- How an audit programme is planned across the year
- How to plan a single audit and prepare a checklist
- How to run the opening meeting
- How to gather evidence through interviews, observation and records
- How to write a clear nonconformity and grade audit findings
- How to run the closing meeting and write the audit report
- How to follow up corrective action after the audit

## How the course works
The course is delivered by e-learning, so you can study from wherever you are. Classes begin on the start date shown on this page.

## How to enrol
Press "Enrol on this course" and send us your details. Our office will reply with the next steps and an invoice.
TEXT;

        // The auditor course keeps its old address until the rename migration has run.
        $descriptions['lead-auditor'] = $descriptions['internal-auditor'];

        return $descriptions;
    }

    public function up(): void
    {
        foreach ($this->descriptions() as $slug => $description) {
            DB::table('courses')->where('slug', $slug)
                ->where(fn ($query) => $query->whereNull('description')->orWhere('description', ''))
                ->update(['description' => $description]);
        }
    }

    public function down(): void
    {
        foreach ($this->descriptions() as $slug => $description) {
            DB::table('courses')->where('slug', $slug)->where('description', $description)->update(['description' => null]);
        }
    }
};
