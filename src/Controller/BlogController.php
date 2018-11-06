<?php
// src/Controller/SorteoController.php
namespace App\Controller;

use App\Entity\Noticia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    public function verNoticia($id)
    {
        // Obtenemos el gestor de entidades de Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        $noticia = $entityManager->getRepository(Noticia::class)->find($id);
        // Si la apuesta no existe lanzamos una excepción.
        if (!$noticia) {
            throw $this->createNotFoundException(
                'No existe ninguna noticia con id ' . $id
            );

        }
        // Pasamos la apuesta a una plantilla que se encargue de mostrar sus datos.
        return $this->render('blog/verNoticia.html.twig', array(
            'noticia' => $noticia,
        ));
    }

    public function borrarNoticia($id)
    {
        // Obtenemos el gestor de entidades de Doctrine
        $entityManager = $this->getDoctrine()->getManager();
        // Obtenenemos el repositorio de Apuestas y buscamos en el usando la id de la noticia

        $noticia = $entityManager->getRepository(Noticia::class)->find($id);
        // Si la apuesta no existe lanzamos una excepción.
        if (!$noticia) {
            throw $this->createNotFoundException(
                'No existe ninguna noticia con id ' . $id
            );
        }
        $entityManager->remove($noticia);
        $entityManager->flush();
        return $this->render('blog/noticiaBorrada.html.twig');
    }

    public function editarNoticia(Request $request, $id)
    {
        // Obtenemos el gestor de entidades de Doctrine

        $entityManager = $this->getDoctrine()->getManager();
        // Obtenenemos el repositorio de Apuestas y buscamos en el usando la id de la noticia

        $apuesta = $entityManager->getRepository(Noticia::class)->find($id);
        // Si la apuesta no existe lanzamos una excepción.
        if (!$apuesta) {
            throw $this->createNotFoundException(
                'No existe ninguna noticia con id ' . $id
            );
        }
        $form = $this->createFormBuilder($apuesta)
            ->add('titular', TextType::class)
            ->add('entradilla', TextType::class)
            ->add('cuerpo', TextareaType::class)
            ->add('fecha', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Editar noticia'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // De esta manera podemos sobreescribir la variable $apuesta con los datos

            $apuesta = $form->getData();
            // Ejecuta las consultas necesarias (UPDATE en este caso)
            $entityManager->flush();
            //Redirigimos a la página de ver la apuesta editada.
            return $this->redirectToRoute('verNoticia', array('id' => $id));
        }
        return $this->render('blog/nuevaNoticia.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function nuevaNoticia(Request $request)
    {
        $noticia = new Noticia();
        // $apuesta->setTexto('2 13 34 44 48'); // Rellenamos el objeto con

        // $apuesta->setFecha(new \DateTime('tomorrow')); // Rellenamos el objeto con

        $form = $this->createFormBuilder($noticia)
            ->add('cuerpo', TextareaType::class)
            ->add('titular', TextType::class)
            ->add('entradilla', TextType::class)
            ->add('fecha', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Añadir Noticia'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $apuesta = $form->getData();
            // Obtenemos el gestor de entidades de Doctrine
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($apuesta);
            // Ejecuta las consultas necesarias (INSERT en este caso)
            $entityManager->flush();
            //Redirigimos a una página de confirmación.
            return $this->redirectToRoute('app_noticia_creada');
        }
        return $this->render('blog/nuevaNoticia.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function noticiaCreada()
    {
        return $this->render('blog/apuestaCreada.html.twig');
    }

    public function index()
    {

        // Obtenemos el gestor de entidades de Doctrine
        $entityManager = $this->getDoctrine()->getManager();
        // obtenemos todas las apuestas
        $noticias = $entityManager->getRepository(Noticia::class)->findAll();
        return $this->render('blog/index.html.twig', array(
            'noticias' => $noticias,
        ));
    }

}