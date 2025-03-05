<?php

$colors = ['red', 'green', 'blue'];

$colorModel = array(
    "red" => '#FF0000',
    'green' => '#00FF00',
    'blue' => '#0000FF'
);

$customers = [
    "customer1" => [
        "name" => "John Doe",
        "email" => "Lb6dQ@example.com",
        "phone" => "123-456-7890",
        "address" => [
            "street" => "123 Main St",
            "city" => "Anytown",
            "state" => "CA",
            "zip" => "12345",
        ]
    ],
    "customer2" => [
        "name" => "Jane Doe",
        "email" => "sY2d8@example.com",
        "phone" => "987-654-3210",
        "address" => [
            "street" => "456 Elm St",
            "city" => "Singapore",
            "state" => "New York",
            "zip" => "54321",
        ]
    ]
];

?>

<body>
    <div class="container"
        style="width: 80%; margin: 0 auto; padding: 20px; background-color: #FFFFFF; box-shadow: 0 8px 8px 8px rgba(0, 0, 0, 0.1); border-radius: 8px;">
        <h1 style="align-items: center; justify-content: center; display: flex;">Customer Information</h1>

        <!-- <div class="customer-card">
            <p style="background-color: <?php echo $colorModel['green']; ?>;">
                <?php echo $customers['customer1']['address']['city']; ?>
            </p>
            <p style="background-color: <?php echo $colorModel['red']; ?>;">
                <?php echo $customers['customer2']['address']['city']; ?>
            </p>
        </div> -->

        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #DAD6D6FF;">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Street Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $key => $customer): ?>
                <?php 
                    // Set background color to red if city is 'Anytown'
                    $cityBackgroundColor = ($customer['address']['city'] == 'Anytown') ? $colorModel['blue'] : $colorModel['green'];
                    ?>
                <tr>
                    <td><?php echo $customer['name']; ?></td>
                    <td><?php echo $customer['email']; ?></td>
                    <td><?php echo $customer['phone']; ?></td>
                    <td><?php echo $customer['address']['street']; ?></td>
                    <td style="background-color: <?php echo $cityBackgroundColor; ?>">
                        <?php echo $customer['address']['city']; ?></td>
                    <td><?php echo $customer['address']['state']; ?></td>
                    <td><?php echo $customer['address']['zip']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>