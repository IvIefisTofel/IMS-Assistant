<?php
namespace Fields\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\ViewModel;

class AdminController extends MCmsController
{
    public function indexAction()
    {
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $fields = $om->getRepository('Fields\Entity\Fields')->findBy(['alias' => ['form_name', 'form']]);
        $fieldIDs = [];
        /* @var $field \Fields\Entity\Fields */
        foreach ($fields as $field) {
            $fieldIDs[$field->getFieldID()] = $field->getAlias();
        }

        $forms = [];
        if (count($fieldIDs) > 0) {
            $arrForms = $om->getRepository('Fields\Entity\FieldsValues')->findBy(['fieldID' => array_keys($fieldIDs)]);
            /* @var $fieldValue \Fields\Entity\FieldsValues */
            foreach ($arrForms as $fieldValue) {
                $forms[$fieldValue->getAlias()][$fieldIDs[$fieldValue->getFieldID()]] = $fieldValue->getValue();
            }
        }

        return new ViewModel([
            'forms' => $forms,
        ]);
    }

    public function editFormAction()
    {
        $alias = $this->params()->fromRoute('alias', null);
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $fields = $om->getRepository('Fields\Entity\Fields')->findBy(['alias' => ['form_name', 'form']], ['fieldID' => 'ASC']);
        $fieldIDs = [];
        /* @var $field \Fields\Entity\Fields */
        foreach ($fields as $field) {
            $fieldIDs[$field->getFieldID()] = $field;
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $wasEdit = false;
            foreach ($fieldIDs as $fID) {
                $fieldValue = $om->getRepository('Fields\Entity\FieldsValues')->findOneBy(['fieldID' => $fID->getFieldID(), 'alias' => $alias]);
                /* @var $fieldValue \Fields\Entity\FieldsValues */
                if ($request->getPost('alias') != $alias) {
                    $fieldValue->setAlias($request->getPost('alias'));
                    $om->persist($fieldValue);
                    $wasEdit = true;
                }
                if ($fieldValue != null && $fieldValue->getValue() != $request->getPost($fID->getAlias())) {
                    $fieldValue->setValue($request->getPost($fID->getAlias()));
                    $om->persist($fieldValue);
                    $wasEdit = true;
                }
            }
            if ($wasEdit) {
                $om->flush();
            }
            return $this->redirect()->toRoute('admin-forms');
        }

        $form = [];
        if (count($fieldIDs) > 0 && $alias != null) {
            $arrForm = $om->getRepository('Fields\Entity\FieldsValues')->findBy(['fieldID' => array_keys($fieldIDs), 'alias' => $alias]);
            if (count($arrForm) == 2) {
                $form['alias'] = [
                    'alias' => 'alias',
                    'label' => 'Имя формы',
                    'type' => 'input',
                    'value' => $alias,
                ];
                /* @var $fieldValue \Fields\Entity\FieldsValues */
                foreach ($arrForm as $fieldValue) {
                    $form[] = [
                        'alias' => $fieldIDs[$fieldValue->getFieldID()]->getAlias(),
                        'label' => $fieldIDs[$fieldValue->getFieldID()]->getName(),
                        'type' => $fieldIDs[$fieldValue->getFieldID()]->getType(),
                        'value' => $fieldValue->getValue(),
                    ];
                }

                return new ViewModel([
                    'form' => $form,
                ]);
            }
        }

        $this->getResponse()->setStatusCode(404);
    }

    public function addFormAction()
    {
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $fields = $om->getRepository('Fields\Entity\Fields')->findBy(['alias' => ['form_name', 'form']], ['fieldID' => 'ASC']);
        $fieldIDs = [];
        /* @var $field \Fields\Entity\Fields */
        foreach ($fields as $field) {
            $fieldIDs[$field->getAlias()] = [
                'fieldID' => $field->getFieldID(),
                'alias' => $field->getAlias(),
                'label' => $field->getName(),
                'type' => $field->getType(),
                'value' => '',
            ];
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formNane = new \Fields\Entity\FieldsValues();
            $formNane->setAlias($request->getPost('alias'));
            $formNane->setFieldID($fieldIDs['form_name']['fieldID']);
            $formNane->setValue($request->getPost('form_name'));
            $om->persist($formNane);

            $formValue = new \Fields\Entity\FieldsValues();
            $formValue->setAlias($request->getPost('alias'));
            $formValue->setFieldID($fieldIDs['form']['fieldID']);
            $formValue->setValue($request->getPost('form'));
            $om->persist($formValue);

            $om->flush();
            return $this->redirect()->toRoute('admin-forms');
        }

        $form = [
            'alias' => [
                'alias' => 'alias',
                'label' => 'Имя формы',
                'type' => 'input',
                'value' => '',
            ],
        ];
        if (count($fieldIDs) > 0) {
            $form = array_merge($form, $fieldIDs);
        }
//        var_dump($form);exit;
        $view = new ViewModel([
            'addForm' => true,
            'form' => $form,
        ]);
        $view->setTemplate('fields/admin/editform');

        return $view;
    }

    public function deleteFormAction()
    {
        $alias = $this->params()->fromRoute('alias', null);
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $fields = $om->getRepository('Fields\Entity\Fields')->findBy(['alias' => ['form_name', 'form']], ['fieldID' => 'ASC']);
        /* @var $field \Fields\Entity\Fields */
        foreach ($fields as $field) {
            $fieldValue = $om->getRepository('Fields\Entity\FieldsValues')->findOneBy(['fieldID' => $field->getFieldID(), 'alias' => $alias]);
            $om->remove($fieldValue);
        }
        $om->flush();

        return $this->redirect()->toRoute('admin-forms');
    }
}