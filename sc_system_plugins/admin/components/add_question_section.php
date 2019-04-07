<?php

class ADMIN_CMP_AddQuestionSection extends SC_Component
{
    public function __construct()
    {
        parent::__construct();

        $language = SC::getLanguage();
        $serviceLang = BOL_LanguageService::getInstance();

        $addSectionForm = new Form('qst_add_section_form');
        $addSectionForm->setAjax();
        $addSectionForm->setAjaxResetOnSuccess(true);
        $addSectionForm->setAction(SC::getRouter()->urlFor("ADMIN_CTRL_Questions", "ajaxResponder"));

        $input = new HiddenField('command');
        $input->setValue('addSection');

        $addSectionForm->addElement($input);

        $qstSectionName = new TextField('section_name');
        $qstSectionName->addAttribute('class', 'sc_text');
        $qstSectionName->addAttribute('style', 'width: auto;');
        $qstSectionName->setRequired();
        $qstSectionName->setLabel($language->text('admin', 'questions_new_section_label'));

        $addSectionForm->addElement($qstSectionName);

        $this->addForm($addSectionForm);

        $addSectionForm->bindJsFunction('success', ' function (result) {
                if ( result.result )
                {
                    SC.info(result.message);
                }
                else
                {
                    SC.error(result.message);
                }

                window.location.reload();
            } ');
    }
}