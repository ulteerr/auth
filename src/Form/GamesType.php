<?php

namespace App\Form;

use App\Entity\Games;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class GamesType extends AbstractType
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
            ->add('img', FileType::class, [
                'label' => 'Загрузить изображение:',
                'mapped' => false,
                'required' => false,
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
            'data_class' => Games::class,
        ]);
    }
}
