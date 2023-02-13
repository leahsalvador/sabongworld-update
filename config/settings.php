<?php
/**
 * Created by Md.Abdullah Al Mamun.
 * Email: mamun1214@gmail.com
 * Date: 9/29/2022
 * Time: 9:43 PM
 * Year: 2022
 */

return [
    'conversion_rate' => env('CONVERSION_RATE', 0.92),
    'max_commission' => env('MAX_COMMISSION', 7),
    'samax_commission' => env('SAMAX_COMMISSION', 5.5),
    'video_block' => env('NO_VIDEO_POINT', 10),
    'minimum_bet' => env('MINIMUM_BET', 10),
    'minimum_deposit' => env('MINIMUM_DEPOSIT', 100),
    'img' => [
        'logo' => env('APP_URL') . "image/" . env('LOGO_IMG', 'default.png'),
    ],
    'commission' => [
        'superadmin' => env('SUPER_ADMIN', 0.0),
        'admin' => env('ADMIN', 0.02),
        'master' => env('MASTER_AGENT', 0.06),
        'sub' => env('SUB_AGENT', 0.01),
        'gold' => env('GOLD_AGENT', 5.5),
        'silver' => env('SILVER_AGENT', 5.5),
        'bronze' => env('BRONZE_AGENT', 5.5),
        'max' => [
            'master' => env('MASTER_MAX', 7),
            'sub' => env('SUB_MAX', 7),
            'gold' => env('GOLD_MAX', 7),
            'silver' => env('SILVER_MAX', 7),
            'bronze' => env('BRONZE_MAX', 7)
        ],
        'min' => [
            'master' => env('MASTER_MIN', 8.5),
            'sub' => env('SUB_MIN', 7),
            'gold' => env('GOLD_MIN', 5),
            'silver' => env('SILVER_MIN', 1),
            'bronze' => env('BRONZE_MIN', 0)
        ],
        'samax' => env('SAMAX_COMMISSION', 5.5),
    ],
    'urls' => [
        'reg' => [
            'agent' => env('AGENT_REG', ''),
            'player' => env('PLAYER_REG', '')
        ],
        'support' => [
            'fb' => env('FB_URL', ''),
            'discord' => env('DISCORD_URL', '')
        ]
    ],
    'bots' => [
        'agent' => env('BOT_AGENT', ""),
        'meron' => env('MERON_BOT_IDS', ""),
        'wala' => env('WALA_BOT_IDS', ""),
        'betting' => [
            'min' => env('BETTING_MIN', "100"),
            'max' => env('BETTING_MAX', "300"),
        ],
    ],
    'role' => [
        'display' => [
            'master-agent' => 'Operator',
            'sub-agent' => 'Sub Operator',
            'gold-agent' => 'Master Agent',
            'silver-agent' => 'Gold Agent',
            'master-agent-player' => 'Operator Player',
            'sub-agent-player' => 'Sub Operator Player',
            'gold-agent-player' => 'Master Agent Player',
            'silver-agent-player' => 'Gold Agent Player',
        ]
    ]
];

