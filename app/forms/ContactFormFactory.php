<?php
declare(strict_types=1);

namespace App\Forms;


use Nette\Application\UI;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Database\Context;
use App\Misc\ConfigParameters;


class ContactFormFactory extends BaseFormFactory
{
    /** @var IMailer */
    private $mailer;

    /** @var ConfigParameters */
    private $configParameters;

    /** @var Context */
    private $database;


    public $onFormSent;


    /**
     * ContactFormFactory constructor.
     * @param IMailer $mailer
     * @param ConfigParameters $configParameters
     * @param Context $database
     */
    public function __construct(IMailer $mailer, ConfigParameters $configParameters, Context $database)
    {
        $this->mailer = $mailer;
        $this->configParameters = $configParameters;
        $this->database = $database;
    }

    /**
     * Create form component
     * @return UI\Form
     */
    public function create()
    {
        $form = new UI\Form;
        // bootstrap renderer
        $this->makeBootstrap3($form);

        $form->addEmail('email', 'E-mail:')->setRequired('Zadejte Váš e-mail');
        $form->addTextArea('message', 'Zpráva:');
        $form->addSubmit('login', 'Odeslat');
        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }


    /**
     * Process sended form
     * @param UI\Form $form
     */
    public function processForm(UI\Form $form)
    {

        $mail = ['customer' => NULL, 'admin' => NULL];

        // customer message
        $mail['customer'] = new Message;
        $mail['customer']->setFrom($this->configParameters->getMailFrom())
            ->addTo($form->values->email)
            ->setSubject('Potvrzení')
            ->setBody("Dobrý den,\nděkujeme za Vaši zprávu.");

        // admin message
        $mail['admin'] = new Message;
        $mail['admin']->setFrom($this->configParameters->getMailFrom())
            ->addTo($this->configParameters->getMailAdmin())
            ->setSubject('Potvrzení')
            ->setBody("Dobrý den,\nna webu " . $this->configParameters->getProjectTitle() . " byla vyplněna následující zpráva" .
                "\n\nE-mail: " . $form->values->email .
                "\nZpráva: " . $form->values->message
            );

        // send mail
        foreach ($mail as $m) {
            $this->mailer->send($m);
        }

    }
}


