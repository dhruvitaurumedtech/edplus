<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .btn {
            color: #FFFFFF !important;
            background-color: #007bff;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;

        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php foreach($data as $value): ?>
        <?php if (isset($value['institute']) && $value['institute'] != ''): ?>
            <p>Dear <?php echo htmlspecialchars($value['firstname']); ?>,</p>
            <p>We are writing to inform you that <?php echo htmlspecialchars($value['lastname']); ?> has expressed interest in enrolling at <?php echo htmlspecialchars($value['institute']); ?> for the upcoming academic term.</p>
            <p>As part of our enrollment process, we require confirmation from a parent or guardian to finalize the admission.</p>
            <p>Please review the following details of the application:</p>
            <p><strong>Student Name:</strong><?php echo htmlspecialchars($value['firstname']); ?>  <?php echo htmlspecialchars($value['lastname']); ?></p>
            <p><strong>Standard:</strong> <?php echo htmlspecialchars($value['standard']); ?></p>
            <p><strong>Subject Applied For:</strong></p>
            <table>
                <tr>
                    <th>Subject Name</th>
                    <th>Fees</th>
                </tr>
                <?php foreach ($value['subjects'] as $sub): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sub['subname']); ?></td>
                        <td><?php echo htmlspecialchars($sub['amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p>"I confirm that my child, <?php echo htmlspecialchars($value['lastname']); ?>, has my permission to enroll at <?php echo htmlspecialchars($value['institute']); ?> for the <?php echo htmlspecialchars($value['year']); ?>. I acknowledge that I have reviewed the enrollment details and agree to the terms and conditions outlined by the institute."</p>
            <a href="<?php echo htmlspecialchars(url('/update-value/' . $value['id'])); ?>" class="btn">Click Here to Verify</a>
            <p>Thank you for your prompt attention to this matter. We look forward to welcoming <?php echo htmlspecialchars($value['lastname']); ?> to our institute.</p>
            <p>Warm regards,</p>
            <p><?php echo htmlspecialchars($value['institutes']); ?></p>
            <p><?php echo htmlspecialchars($value['address']); ?></p>
            <p><?php echo htmlspecialchars($value['Iemail']); ?></p>
            <p><?php echo htmlspecialchars($value['contact_no']); ?></p>
            <p><?php echo htmlspecialchars($value['website_link']); ?></p>
        <?php else: ?>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($value['email']); ?></p>
            <p><strong>Password:</strong> <?php echo htmlspecialchars($value['password']); ?></p>
        <?php endif; ?>
        <hr>
    <?php endforeach; ?>
</body>
</html>
