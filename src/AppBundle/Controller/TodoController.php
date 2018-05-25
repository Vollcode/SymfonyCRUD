<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Todo;

class TodoController extends Controller
{
  /**
  * @Route("/todolist/web", name="todo_list")
  */
  public function listAction(Request $request){
    $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')
    ->findAll();

    return $this->render('default/index.html.twig',['todos'=>$todos]);
  }

  /**
  * @Route("/todolist/create", name="todo_create")
  */
  public function createAction(Request $request){
    $todo = new Todo;

    $form = $this->createFormBuilder($todo)
       ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
       ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
       ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
       // ->add('priority', ChoiceType::class, array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'High'=>'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
       ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
       ->add('Save', SubmitType::class, array('label'=> 'Create Todo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
       ->getForm();
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
      $name=$form['name']->getData();
      $category=$form['category']->getData();
      $description=$form['description']->getData();
      $due_date=$form['due_date']->getData();

      $now= new\DateTime('now');

      $todo->setName($name);
      $todo->setCategory($category);
      $todo->setDescription($description);
      $todo->setCreateDate($now);
      $todo->setDueDate($due_date->format('Y-m-d H:i:s'));

      $data= $this->getDoctrine()->getManager();
      $data->persist($todo);
      $data->flush();

      $this->addFlash('notice','Todo added');

      return $this->redirectToRoute('todo_list');
    }

    return $this->render('default/create.html.twig',['form'=>$form->createView()]);
  }

  /**
  * @Route("/todolist/details/{id}", name="todo_details")
  */
  public function detailsAction($id){
    $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')
    ->find($id);

    return $this->render('default/details.html.twig',['todo'=>$todos]);
  }

  /**
     * @Route("/todolist/edit/{id}",name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $now = new\DateTime('now');
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

        $todo->setName($todo->getName());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription($todo->getDescription());
        $todo->setCreateDate($now);

        $date = strtotime($todo->getDueDate());
        echo date('d/M/Y H:i:s', $date);
        $todo->setDueDate($date);

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))

            ->add('due_date', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('Save', SubmitType::class, array('label'=> 'Update Todo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $due_date = $form['due_date']->getData();

            $now = new\DateTime('now');

            $data = $this->getDoctrine()->getManager();
            $todo = $data->getRepository("AppBundle:todo")->find($id);

            $todo->setName($name);
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setCreateDate($now);
            $todo->setDueDate($due_date);


            $data->flush();

            $this->addFlash('notice', 'Todo Actualizado');
            return $this->redirectToRoute('todo_list');
        }
        return $this->render('default/edit.html.twig', array(
            'todo'=>$todo,
            'form'=>$form->createView()
        ));

    }


  /**
  * @Route("/todolist/delete/{id}", name="todo_delete")
  */
  public function deleteAction($id){
    $data = $this->getDoctrine()->getManager();
    $todo = $data->getRepository('AppBundle:Todo')->find($id);

    $data->remove($todo);
    $data->flush();

    $this->addFlash('notice','Todo deleted');

    return $this->redirectToRoute('todo_list');
  }


}
