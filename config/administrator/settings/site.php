<?php

use Illuminate\Support\Facades\Auth;

return [
    'title' => '站点设置',

    'permission' => function() {
        return Auth::user()->hasRole('Founder');
    },

    // 站点配置表单
    'edit_fields' => [
        'site_name' => [
            // 表单标题
            'title' => '站点名称',

            // 表单条目类型
            'type' => 'text',

            // 字数限制
            'limit' => 50
        ],

        'contact_email' => [
            'title' => '联系人邮箱',
            'type'  => 'text',
            'limit' => 50
        ],
        'seo_description' => [
            'title' => 'SEO - Description',
            'type' => 'textarea',
            'limit' => 250,
        ],
        'seo_keyword' => [
            'title' => 'SEO - Keywords',
            'type' => 'textarea',
            'limit' => 250,
        ],
    ],

    // 表单验证规则
    'roles' => [
        'site_name' => 'required|max:50',
        'contact_email' => 'email',
    ],
    'messages' => [
        'site_name.required' => '请填写站点名称。',
        'contact_email.email' => '请填写正确的联系人邮箱格式。',
    ],

    // 数据即将保存时触发的钩子，可以对用户提交的数据做修改
    'before_save' => function($data) {

        // 为网站名称加上后缀，可以对用户提交的数据做修改
        if (strpos($data['site_name'], 'Powered by LaraBBS') === false) {
            $data['site_name'] .= ' - Powered by LaraBBS';
        }
    },

    // 你可以自定义多个动作，每一个动作为设置页面底部的『其他操作』区块
    'actions' => [
        'clear_chche' => [
            'title' => '更新系统缓存',

            // 不同状态时页面提醒
            'messages' => [
                'active'  => '正在清理缓存',
                'success' => '缓存已清空',
                'error'   => '情况缓存时出错',
            ],

            // 动作执行代码，注意你可以通过修改 $data 参数更改配置信息
            'action' => function($data) {
                \Artisan::call('cache:clear');
                return true;
            }
        ]
    ]
];

