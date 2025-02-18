<!DOCTYPE html>
<html>
<head>
    <title>Booking Status Update</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2>Booking Status Update</h2>
        <p>Dear <?= $user['name'] ?>,</p>
        
        <p>The status of your booking has been updated. Here are the details:</p>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Booking Title:</strong> <?= $booking['title'] ?></p>
            <p><strong>Date:</strong> <?= date('l, F j, Y', strtotime($booking['start_time'])) ?></p>
            <p><strong>Time:</strong> <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></p>
            <?php if (!empty($booking['description'])): ?>
                <p><strong>Description:</strong> <?= $booking['description'] ?></p>
            <?php endif; ?>
            <p><strong>Status:</strong> 
                <?php
                $color = '';
                switch($booking['status']) {
                    case 'approved':
                        $color = '#27ae60';
                        break;
                    case 'rejected':
                        $color = '#c0392b';
                        break;
                    case 'cancelled':
                        $color = '#7f8c8d';
                        break;
                    default:
                        $color = '#f39c12';
                }
                ?>
                <span style="color: <?= $color ?>; text-transform: capitalize;"><?= $booking['status'] ?></span>
            </p>
        </div>
        
        <?php if ($booking['status'] === 'approved'): ?>
            <p>Your booking has been approved. We look forward to seeing you!</p>
        <?php elseif ($booking['status'] === 'rejected'): ?>
            <p>Unfortunately, your booking has been rejected. If you have any questions, please contact us.</p>
        <?php endif; ?>
        
        <p>If you need to make any changes or have questions, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <?= getenv('APP_NAME') ?> Team</p>
    </div>
</body>
</html>
