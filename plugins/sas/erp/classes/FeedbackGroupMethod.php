<?php

namespace Sas\Erp\Classes;


use Backend\Widgets\Form;
use Sas\Erp\Controllers\FeedbackChannels;
use Sas\Erp\Models\FeedbackChannel;
use Illuminate\Support\Facades\Mail;

class FeedbackGroupMethod implements FeedbackMethod
{

    public function boot()
    {
        FeedbackChannels::extendFormFields(function (Form $form, $model) {
            $form->addFields([
                    'method_data[channels]' => [
                        'label' => "sas.erp::lang.channel.many",
                        'commentAbove' => "sas.erp::lang.method.group.channels_comment",
                        'type' => 'checkboxlist',
                        'options' => FeedbackChannel::all()->lists('name', 'id'),
                        'required' => true,
                        'trigger' => [
                            'action' => "show",
                            'field' => "method",
                            'condition' => "value[group]"
                        ]
                    ]
                ]
            );
        });
    }

    public function send($methodData, $data)
    {
        foreach ($methodData['channels'] as $channelId) {
            /** @var Channel $channel */
            $channel = FeedbackChannel::find($channelId);
            if ($channel) {
                $channel->prevent_save_database = true;
                $channel->send($data);

                $channel->prevent_save_database = $channel->getOriginal('prevent_save_database');
            }
            else {
                \Log::warning('FeedbackPlugin: One of your group channels is trying to send a message to an unknown channel.');
            }
        }
    }

}
