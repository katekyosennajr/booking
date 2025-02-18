<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2>Booking Confirmation</h2>
        <p>Dear <?= $user['name'] ?>,</p>
        
        <p>Your booking has been received and is currently pending approval. Here are the details:</p>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Booking Title:</strong> <?= $booking['title'] ?></p>
            <p><strong>Date:</strong> <?= date('l, F j, Y', strtotime($booking['start_time'])) ?></p>
            <p><strong>Time:</strong> <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></p>
            <?php if (!empty($booking['description'])): ?>
                <p><strong>Description:</strong> <?= $booking['description'] ?></p>
            <?php endif; ?>
            <p><strong>Status:</strong> <span style="color: #f39c12;">Pending</span></p>
        </div>
        
        <p>We will notify you once your booking has been reviewed.</p>
        
        <p>If you have any questions, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <?= getenv('APP_NAME') ?> Team</p>
    </div>
</body>
</html>
