<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Notice;
use AppBundle\Entity\Comment;
use AppBundle\Form\NoticeType;
use AppBundle\Form\CommentType;
use \DateTime;

/**
 * Notice controller.
 *
 * @Route("notice")
 */
class NoticeController extends Controller
{

    /**
     * Lists all Notice entities.
     *
     * @Route("/", name="")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $req)
    {
        //show by category
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Category')->findAll();

        $category = $req->query->get('category');

        if (isset($category)) {
            $entities = $em->getRepository('AppBundle:Category')->findOneBy(['name' => $category])->getNotices();
        } else {
            $entities = $em->getRepository('AppBundle:Notice')->findAll();
        }

        $result = [];
        foreach ($entities as $entity) {
            if ((new DateTime(date("Y-m-d H:i:s", time()))) >= $entity->getEnd()) {
                $entity->setStatus('Non Active');
                $em->persist($entity);
                $em->flush();
            } else {
                array_push($result, $entity);
            }

        }


        return array(
            'entities' => $result,
            'categories' => $categories,
        );
    }

    /**
     * Creates a new Notice entity.
     *
     * @Route("/", name="_create")
     * @Method("POST")
     * @Template("AppBundle:Notice:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Notice();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setStart(new DateTime(date("Y-m-d H:i:s")));
            $entity->setEnd(new DateTime(date("Y-m-d H:i:s", (time() + (int)$form->getData()->getEnd()))));

            $photo = $entity->getPhoto();
            if ($photo) {
                $photoName = md5(uniqid()) . '.' . $photo->guessExtension();
                $photo->move($this->getParameter('photo_directory'), $photoName);
                $entity->setPhoto($photoName);
            }

            $user = $this->container->get('security.context')->getToken()->getUser();
            $entity->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Notice entity.
     *
     * @param Notice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Notice $entity)
    {
        $form = $this->createForm(new NoticeType(), $entity, array(
            'action' => $this->generateUrl('_create'),
            'method' => 'POST',
        ));
        $form->add('end', 'choice', [
            'choices' => [
                '1 day' => '86400',
                '3 days' => '259200',
                '5 days' => '432000',
                '7 days' => '604800',
                '14 days' => '1209600',
            ],
            'choices_as_values' => true,
        ]);
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Notice entity.
     * @Security("has_role('ROLE_USER')")
     * @Route("/new", name="_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Notice();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Notice entity.
     *
     * @Route("/{id}", name="_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Notice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notice entity.');
        }


        //Add coment form
        $comment = new Comment();
        $commentForm = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('comment_create', ['notice_id' => $id]),
            'method' => 'POST',
        ));
        $commentForm->add('submit', 'submit', array('label' => 'Add comment'));

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'comment_form' => $commentForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Notice entity.
     *
     * @Route("/{id}/edit", name="_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Notice')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notice entity.');
        }

        $start = $entity->getStart()->format('Y-m-d H:i:s');
        $entity->setStart($start);

        $editForm = $this->createEditForm($entity);

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Notice entity.
     *
     * @param Notice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Notice $entity)
    {
        $form = $this->createForm(new NoticeType(), $entity, array(
            'action' => $this->generateUrl('_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('end');
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Notice entity.
     *
     * @Route("/{id}", name="_update")
     * @Method("PUT")
     * @Template("AppBundle:Notice:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Notice')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notice entity.');
        }
        $photopath = $entity->getPhoto();

        $start = $entity->getStart()->format('Y-m-d H:i:s');
        $entity->setStart($start);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $editForm->getData()->setPhoto($photopath);

        if ($editForm->isValid()) {
            $entity->setStart(new DateTime(date("Y-m-d H:i:s", (int)$editForm->getData()->getStart())));


            $em->flush();

            return $this->redirect($this->generateUrl('_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Notice entity.
     *
     * @Route("/{id}", name="_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Notice')->find($id);


            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Notice entity.');
            }

            $em->remove($entity);

            $em->flush();
        }

        return $this->redirect($this->generateUrl(''));
    }

    /**
     * Creates a form to delete a Notice entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }


    /**
     * @Route("archive/", name="_archive")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_USER')")
     */
    public function archiveShowAction()
    {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Notice')->findAll();
        $result = [];
        foreach ($entities as $entity) {
            if ((new DateTime(date("Y-m-d H:i:s", time()))) >= $entity->getEnd()) {
                $entity->setStatus('Non Active');

                if ($this->getUser() == $entity->getUser() || $this->isGranted('ROLE_ADMIN')) {
                    array_push($result, $entity);

                }

                $em->persist($entity);
                $em->flush();
            }

        }
        return array(
            'entities' => $result,
        );

    }

}
