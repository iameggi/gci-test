<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GradeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $student;
    public $assignmentTitle;
    public $courseName;
    public $score;


    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
        $this->student = $submission->student;
        $this->assignmentTitle = $submission->assignment->title;
        $this->courseName = $submission->assignment->course->name;
        $this->score = $submission->score;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nilai Baru: ' . $this->assignmentTitle,
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'email.grade',
        );
    }


    public function attachments(): array
    {
        return [];
    }
}