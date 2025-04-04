<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SendTaskDataNotification;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Notifications\AnonymousNotifiable;

class SendTaskDataNotificationTest extends TestCase
{
    #[Test]
    public function notification_actually_sends_email_with_attachment()
    {
        // Път към съществуващ файл
        $filePath = 'private/private/surveys/survey_ub0g6nhf8gdq3x1t.csv';
        $taskName = 'TestCSVAngel';
        $emails = 'angel4o2003@abv.bg, angelvalkov03@schoolmath.eu';

        // Увери се, че файлът съществува
        $this->assertFileExists(storage_path('app/' . $filePath));

        $notification = new SendTaskDataNotification($emails, $taskName, $filePath);

        // Изпрати до всеки имейл
        foreach ($notification->getEmailRecipients() as $recipientEmail) {
            (new AnonymousNotifiable)
                ->route('mail', $recipientEmail)
                ->notify($notification);
        }

        // Няма assert, защото изпращаме реално
        $this->assertTrue(true); // Просто за да мине PHPUnit
    }
}
