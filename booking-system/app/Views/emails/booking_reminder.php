<!DOCTYPE html>
<html>
<head>
    <title>Booking Reminder</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2>Booking Reminder</h2>
        <p>Dear <?= $user['name'] ?>,</p>
        
        <p>This is a reminder for your upcoming booking:</p>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Booking Title:</strong> <?= $booking['title'] ?></p>
            <p><strong>Date:</strong> <?= date('l, F j, Y', strtotime($booking['start_time'])) ?></p>
            <p><strong>Time:</strong> <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></p>
            <?php if (!empty($booking['description'])): ?>
                <p><strong>Description:</strong> <?= $booking['description'] ?></p>
            <?php endif; ?>
        </div>
        
        <p>We look forward to seeing you!</p>
        
        <p>If you need to make any changes or have questions, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <?= getenv('APP_NAME') ?> Team</p>
    </div>
</body>
</html>
