<?php

namespace App\Controller;

use App\Entity\SMSOut;
use App\Form\SMSOutType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\SMSIn;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\SendMessage;

class SMSInController extends Controller
{
    /**
     * @Route("/sms/receive", name="receive_sms")
     */
    public function receiveAction(Request $request, SendMessage $sendMessage)
    {
        //access the get parameters, %p for sms_origin and %a for message
        $message = $request->query->get('message');
        $sms_origin = $request->query->get('phone_no');

        // date time for when this message was received
        $now = new \DateTime("now");

        // set values to a new SMSIn entity
        $smsIn = new SMSIn();
        $smsIn->setReceivedOn($now);
        $smsIn->setSmsOrigin($sms_origin);
        $smsIn->setWholeSms($message);
        $this->save($smsIn);
        $send = $sendMessage->sendMessage('0705285959', $message);

        return $this->render('sms_in/index.html.twig', ['message' => $message]);
    }

    /**
     * @Route("/", name="list_messages")
     */
    public function listAction(Request $request)
    {
        // display all entries from the mpesa table.
        $data = [];
        $messages = $this->em()->getRepository('App:SMSIn')
            ->findAll();
        $sorted_messages = [];
        foreach ($messages as $key => $message) {
            $sorted_messages[$message->getSmsOrigin()] = $message;
        }
        $data['sorted_messages'] = $sorted_messages;
        $data['messages'] = $messages;
        return $this->render('sms_in/messages.html.twig', $data);
    }

    /**
     * @Route("/sms/chat/{number}", name="chat", methods="GET|POST")
     */
    public function chatAction(Request $request, SendMessage $sendMessage, $number)
    {
        // display all entries from the mpesa table.
        $data = [];
        $messages_in = $this->em()->getRepository('App:SMSIn')
            ->findBy(
                array('sms_origin' => $number),
                array('id' => 'DESC')
            );

        $messages_out = $this->em()->getRepository('App:SMSOut')
            ->findBy(
                array('send_to' => $number),
                array('id' => 'DESC')
            );

        $data['messages_in'] = $messages_in;
        $data['messages_out'] = $messages_out;
        $data['number'] = $number;

        $sMSOut = new SMSOut();
        $sMSOut->setSendTo($number);
        $form = $this->createForm(SMSOutType::class, $sMSOut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            // var_dump($form_data);
            $phone_no = $form_data->getSendTo();
            $message = $form_data->getMessage();
            $now = new \DateTime("now");

            $send = $sendMessage->sendMessage($phone_no, $message);
            $this->addFlash('success', "Message sent");

            $em = $this->getDoctrine()->getManager();
            $sMSOut->setReceivedOn($now);
            $em->persist($sMSOut);
            $em->flush();

            return $this->redirectToRoute('sms_out_index');
        }

        return $this->render('sms_in/chat.html.twig', [
            'sms_out' => $sMSOut,
            'form' => $form->createView(),
            'data' => $data
     ]);
    }

    /**
     * @Route("/sms/clear", name="clear_messages")
     */
    public function clearAction(Request $request)
    {
        // this is destructive! it will clear the database and the log file.
        $messages = $this->em()->getRepository('App:SMSIn')
            ->findAll();
        foreach($messages as $message){
            $this->em()->remove($message);
            $this->em()->flush();
        }
        $date = date('Y-m-d');
        $path_to_log = $this->container->get('kernel')->getLogDir();
        $mpesa_log = "sms-".$date.".log";

        file_put_contents($path_to_log."/".$mpesa_log, "");
        return $this->render('sms_in/delete.html.twig');
    }

    private function save($entity){
        $this->em()->persist($entity);
        $this->em()->flush();        
    } 
    
    private function em(){
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

}