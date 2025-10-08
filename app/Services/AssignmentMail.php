<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;
    public $student;
    public $courseName;
    public $lecturerName;


    public function __construct(Assignment $assignment, User $student)
    {
        $this->assignment = $assignment;
        $this->student = $student;
        $this->courseName = $assignment->course->name;
        $this->lecturerName = $assignment->course->lecturer->name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tugas Baru: ' . $this->assignment->title,
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'email.assigment',
        );
    }

  
    public function attachments(): array
    {
        return [];
    }
}