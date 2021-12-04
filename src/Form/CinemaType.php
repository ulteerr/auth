<?php

namespace App\Form;

use App\Entity\Cinema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class CinemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' =>'Введите название:'
            ])
            ->add('body', TextareaType::class,[
                'label' =>'Введите описание:'
            ])
            ->add('img', DropzoneType::class, [
                'attr' => [
                    'placeholder' => 'Загрузить изображение',
                ],
                'label' => 'Загрузить изображение:',


                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'Не подходящее изображение',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cinema::class,
        ]);
    }
}
