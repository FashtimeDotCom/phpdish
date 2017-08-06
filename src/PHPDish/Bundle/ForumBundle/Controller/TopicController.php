<?php
namespace PHPDish\Bundle\ForumBundle\Controller;

use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use PHPDish\Bundle\ForumBundle\Form\Type\TopicType;
use PHPDish\Bundle\ForumBundle\Service\ReplyManagerInterface;
use PHPDish\Bundle\ForumBundle\Service\TopicManagerInterface;
use PHPDish\Bundle\UserBundle\Service\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TopicController extends Controller
{
    /**
     * @Route("/topics", name="topic")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getTopicManager();
        $criteria = Criteria::create();
        $criteria->orderBy([
            'recommended' => 'desc',
            'createdAt' => 'desc'
        ]);
        $topics = $manager->findTopics($criteria, $request->query->getInt('page', 1));
        return $this->render('PHPDishWebBundle:Topic:index.html.twig', [
            'topics' => $topics
        ]);
    }

    /**
     * @Route("/topics/new", name="topic_add")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $manager = $this->getTopicManager();
        $topic = $manager->createTopic($this->getUser());
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->saveTopic($topic);
            return $this->redirectToRoute('topic_view', [
                'id' => $topic->getId()
            ]);
        }
        return $this->render('PHPDishWebBundle:Topic:create.html.twig',  [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/topics/{id}", name="topic_view", requirements={"id": "\d+"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function viewAction($id, Request $request)
    {
        $topic = $this->getTopicManager()->findTopicById($id);
        $replies = $this->getReplyManager()->findTopicReplies($topic, $request->query->getInt('page', 1));
        return $this->render('PHPDishWebBundle:Topic:view.html.twig', [
            'topic' => $topic,
            'replies' => $replies
        ]);
    }

    /**
     * @Route("/users/{username}/topics", name="user_topics")
     * @param string $username
     * @param Request $request
     * @return Response
     */
    public function userTopicsAction($username, Request $request)
    {
        $user = $this->getUserManager()->findUserByName($username);
        $topics = $this->getTopicManager()->findUserTopics($user, $request->query->getInt('page', 1));
        return $this->render('PHPDishWebBundle:Topic:user_topics.html.twig', [
            'user' => $user,
            'topics' => $topics
        ]);
    }

    /**
     * 今日热帖
     * @param int|null $max
     * @return Response
     */
    public function todayHotTopicsAction($max = null)
    {
        $date = Carbon::today()->modify('-100 days');
        $topics = $this->getTopicManager()->findHotTopics($date, $max ?: 10);
        return $this->render('PHPDishWebBundle:Topic:today_hot.html.twig', [
            'topics' => $topics
        ]);
    }

    /**
     * @return TopicManagerInterface
     */
    protected function getTopicManager()
    {
        return $this->get('phpdish.manager.topic');
    }

    /**
     * @return ReplyManagerInterface
     */
    protected function getReplyManager()
    {
        return $this->get('phpdish.manager.reply');
    }

    /**
     * @return UserManagerInterface
     */
    protected function getUserManager()
    {
        return $this->get('phpdish.manager.user');
    }
}
