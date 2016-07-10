<?php
namespace Files\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class AdminController extends MCmsController
{
    public function indexAction()
    {
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $oFields = $om->getRepository('Fields\Entity\Fields')->findByAlias(['file', 'image']);
        $arrValidIndexes = [];
        foreach ($oFields as $oField) {
            /* @var $oField \Fields\Entity\Fields */
            $arrValidIndexes[$oField->getAlias()] = $oField->getFieldID();
        }

        $form = new \Files\Form\FileUpload('uploadFiles');

        $request = $this->getRequest();
        if($request->isPost()) {

            $files = $request->getFiles()->toArray();

            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $files
            );

            $form->setData($data);
            if ($form->isValid()) {

                $formData = $form->getData();

                $arrFiles = [];
                foreach ($formData[\Files\Form\FileUpload::FILES] as $file) {
                    // @todo construct name with directories
                    $fName = str_replace('\\', '/', preg_replace('/[\"\*\:\<\>\?\'\|]+/', '', $file['name']));

                    $prefix = 1;
                    while ($om->getRepository('Fields\Entity\FieldsValues')->findOneByAlias($fName) != null || in_array($fName, $arrFiles)) {
                        $info = pathinfo($fName);
                        $dir = (string)substr($info['dirname'], 2);
                        $dir = (strlen($dir) > 0) ? $dir . '/' : $dir;
                        $fName = $dir . preg_replace('/_{0,1}\([\d]+\)/', '', $info['filename']) . "_($prefix)." . $info['extension'];
                        $prefix++;
                    }
                    $arrFiles[] = $fName;

                    $oFieldValue = new \Fields\Entity\FieldsValues();
                    $oFieldValue->setFieldID(
                        ((strpos(mime_content_type($file['tmp_name']), 'image') !== false))
                            ? $arrValidIndexes['image']
                            : $arrValidIndexes['file']
                    );
                    $oFieldValue->setAlias($fName);
                    $oFieldValue->setValue($file['tmp_name']);

                    $om->persist($oFieldValue);
                }
                $om->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonModel([
                        'error' => false,
                    ]);
                }
            } elseif ($request->isXmlHttpRequest()) {
                return new JsonModel([
                    'error' => false,
                ]);
            }
        }

        $arrFiles = $om->getRepository('Fields\Entity\FieldsValues')->findByFieldID($arrValidIndexes, ['alias' => 'ASC']);

        $view = new ViewModel([
            'form' => $form,
            'arrFiles' => $arrFiles,
            'arrValidIndexes' => $arrValidIndexes,
        ]);
        return $view;
    }

    public function ajaxFileEditAction()
    {
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $request = $this->getRequest();
        if($request->isXmlHttpRequest() && $request->isPost()) {
            $data = $request->getPost();

            switch ($data['action']) {
                case 'editFile': {
                    $error = false;
                    $message = 'Файл успешно изменен.';
                    $newAlias = null;
                    foreach ($data['data'] as $value) {
                        $data[$value['name']] = $value['value'];
                    }

                    if (!isset($data['fileAlias'])) {
                        $error = true;
                        $message = 'Missing file alias.';
                    } else {
                        /* @var $oFile \Fields\Entity\FieldsValues */
                        $oFile = $om->getRepository('Fields\Entity\FieldsValues')->findOneByAlias($data['fileAlias']);
                        if ($oFile == null) {
                            $error = true;
                            $message = 'FiledValue not found in database.';
                        } else {
                            $oFile->setAlias($data['fileNewAlias']);
                            $om->persist($oFile);
                            $om->flush();
                            $newAlias = $data['fileNewAlias'];
                        }
                    }

                    return new JsonModel([
                        'error' => $error,
                        'message' => $message,
                        'newAlias' => $newAlias,
                    ]);
                    break;
                }
                case 'deleteFile': {
                    $error = false;
                    $message = 'Файл успешно удален.';

                    if (!isset($data['data'])) {
                        $error = true;
                        $message = 'Missing file alias.';
                    } else {
                        /* @var $oFile \Fields\Entity\FieldsValues */
                        $oFile = $om->getRepository('Fields\Entity\FieldsValues')->findOneByAlias($data['data']);
                        if ($oFile == null) {
                            $error = true;
                            $message = 'FiledValue not found in database.';
                        } else {
                            if (file_exists($oFile->getValue())) {
                                unlink($oFile->getValue());
                            }
                            $om->remove($oFile);
                            $om->flush();
                        }
                    }

                    return new JsonModel([
                        'error' => $error,
                        'message' => $message,
                    ]);
                    break;
                }
                case 'deleteManyFiles': {
                    $error = false;
                    $message = 'Missing file alias.';

                    if (!isset($data['data'])) {
                        $error = true;
                    } else {
                        $message = count($data['data']) . ' файл успешно удален';

                        $oFiles = $om->getRepository('Fields\Entity\FieldsValues')->findByAlias($data['data']);
                        if ($oFiles == null) {
                            $error = true;
                            $message = 'FiledValue not found in database.';
                        } else {
                            foreach ($oFiles as $oFile) {
                                /* @var $oFile \Fields\Entity\FieldsValues */
                                if (file_exists($oFile->getValue())) {
                                    unlink($oFile->getValue());
                                }
                                $om->remove($oFile);
                            }
                            $om->flush();
                        }
                    }

                    return new JsonModel([
                        'error' => $error,
                        'message' => $message,
                    ]);
                    break;
                }
                default: {
                    return new JsonModel(array(
                        'error' => true,
                        'message' => 'Unknown action parameter.',
                    ));
                    break;
                }
            }
        }

        return new JsonModel(array(
            'error' => true,
            'message' => 'Unknown action parameter.',
        ));
    }
}