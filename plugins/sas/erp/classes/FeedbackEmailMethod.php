<?php
namespace Sas\Erp\Classes;


use Backend\Models\User;
use Backend\Widgets\Form;
use Sas\Erp\Controllers\FeedbackChannels;
use Sas\Erp\Models\FeedbackChannel;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class FeedbackEmailMethod implements FeedbackMethod
{

    public function boot()
    {
        FeedbackChannels::extendFormFields(function (Form $form, $model) {
            $form->addFields([
                    'method_data[email_destination]' => [
                        'label' => "sas.erp::lang.feedback.method.email.destination",
                        'commentAbove' => "sas.erp::lang.feedback.method.email.destination_comment",
                        'required' => true,
                        'trigger' => [
                            'action' => "show",
                            'field' => "method",
                            'condition' => "value[email]"
                        ]
                    ],
                    'method_data[subject]' => [
                        'label' => "sas.erp::lang.feedback.method.email.subject",
                        'required' => true,
                        'trigger' => [
                            'action' => "show",
                            'field' => "method",
                            'condition' => "value[email]"
                        ]
                    ],
                    'method_data[template]' => [
                        'type' => 'codeeditor',
                        'language' => 'twig',
                        'label' => "sas.erp::lang.feedback.method.email.template",
                        'commentAbove' => 'sas.erp::lang.feedback.method.email.template_comment',
                        'required' => true,
                        'trigger' => [
                            'action' => "show",
                            'field' => "method",
                            'condition' => "value[email]"
                        ]
                    ]
                ]
            );
        });

        FeedbackChannel::extend(function(FeedbackChannel $model) {
            $model->rules['method_data.email_destination'] = "emails";
        });
    }

    public function send($methodData, $data)
    {
        $sendTo = $methodData['email_destination'];
        if ($sendTo == null) {
            // find the first admin user on the system
            $sendTo = $this->findAdminEmail();
        }

        $loader = new \Twig_Loader_Array(array(
            'subject' => $methodData['subject'],
            'main' => $methodData['template']
        ));
        $twig = new \Twig_Environment($loader);

        $data['serverName'] = \Request::instance()->server('SERVER_NAME');
        $data['host'] = \Request::instance()->getSchemeAndHttpHost();

        $subject = $twig->render('subject', $data);
        Mail::queue('sas.erp::base-email', ['content' => $twig->render('main', $data)], function (Message $message) use ($sendTo, $subject, $data) {
            $message->subject($subject);
            $message->to(array_map('trim', explode(',', $sendTo)));

            $replyTo = isset($data['email']) ? $data['email'] : null;
            $replyToName = isset($data['name']) ? $data['name'] : 'Guest';
            if ($replyTo) {
                $message->replyTo($replyTo, $replyToName);
            }
        });
    }

    /**
     * @return mixed
     * @throws \ErrorException
     */
    private function findAdminEmail()
    {
        $sendTo = false;

        $users = User::all();
        foreach ($users as $user) {
            if ($user->isSuperUser()) {
                $sendTo = $user->email;
                break;
            }
        }

        if ($sendTo === false) {
            throw new \ErrorException('None email registered neither exists an admin user on the system (!?)');
        }

        return $sendTo;
    }

}
