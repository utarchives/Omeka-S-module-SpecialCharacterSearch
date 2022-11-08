<?php
namespace SpecialCharacterSearch\Controller\Admin;

use SpecialCharacterSearch\CsvFile;
use SpecialCharacterSearch\Form\BatchForm;
use SpecialCharacterSearch\Form\ImportForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $config;
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    public function indexAction()
    {
        $view = new ViewModel();
        $form = $this->getForm(ImportForm::class);
        $view->form = $form;
        $response = $this->api()->search('character_maps', ['sort_by' => 'id', 'sort_order' => 'asc']);
        $maps = $response->getContent();
        $view->setVariable('maps', $maps);
        return $view;
    }
    public function mapImportAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
            $tmpFile = $post['csv']['tmp_name'];
            $csvFile = new CsvFile($this->config);
            $csvPath = $csvFile->getTempPath();
            $csvFile->moveToTemp($tmpFile);
            $csvFile->loadFromTempPath();
            $isUtf8 = $csvFile->isUtf8();
            if (! $csvFile->isUtf8()) {
                $this->messenger()->addError('File is not UTF-8 encoded.'); // @translate
                return $this->redirect()->toRoute('admin/special-character-search');
            }
            $csv['csvpath'] = $csvPath;
            $dispatcher = $this->jobDispatcher();
            $job = $dispatcher->dispatch('SpecialCharacterSearch\Job\ImportCharacterMap', $csv);
            $this->messenger()->addSuccess('Importing in Job ID ' . $job->getId()); // @translate
        }
        $view = new ViewModel();
        $form = $this->getForm(ImportForm::class);
        $view->form = $form;
        return $view;
    }
    public function createSearchValueAction() {
        if ($this->getRequest()->isPost()) {
            $dispatcher = $this->jobDispatcher();
            $job = $dispatcher->dispatch('SpecialCharacterSearch\Job\CreateSearchValue', []);
            $this->messenger()->addSuccess('Creating in Job ID ' . $job->getId()); // @translate
        }
        $view = new ViewModel();
        $form = $this->getForm(BatchForm::class);
        $view->form = $form;
        return $view;
    }
    public function createSearchItemAction() {
        if ($this->getRequest()->isPost()) {
            $dispatcher = $this->jobDispatcher();
            $job = $dispatcher->dispatch('SpecialCharacterSearch\Job\CreateSearchItem', []);
            $this->messenger()->addSuccess('Creating in Job ID ' . $job->getId()); // @translate
        }
        $view = new ViewModel();
        $form = $this->getForm(BatchForm::class);
        $view->form = $form;
        return $view;
    }

}

