<?php

function get_member($id) {
    $members = [
        1 => [
            'id' => 1,
            'name' => 'John Doe',
            'role' => 'Project Manager',
            'email' => 'john@example.com',
            'image' => 'john_doe.jpg'
        ],
        2 => [
            'id' => 2,
            'name' => 'Jane Smith',
            'role' => 'Lead Developer',
            'email' => 'jane@example.com',
            'image' => 'jane_smith.jpg'
        ],
    ];

    return isset($members[$id]) ? $members[$id] : null;
}
?>
