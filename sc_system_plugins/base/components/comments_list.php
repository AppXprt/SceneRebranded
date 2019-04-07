<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.scene.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Scene software.
 * The Initial Developer of the Original Code is Scene Foundation (http://www.scene.org/foundation).
 * All portions of the code written by Scene Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Scene Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Scene community software
 * Attribution URL: http://www.scene.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package ow.sc_system_plugins.base.components
 * @since 1.0
 */
class BASE_CMP_CommentsList extends SC_Component
{
    /**
     * @var BASE_CommentsParams
     */
    protected $params;
    protected $batchData;
    protected $staticData;
    protected $id;
    protected $commentCount;
    protected $cmpContextId;

    /**
     * @var BOL_CommentService
     */
    protected $commentService;
    protected $avatarService;
    protected $page;
    protected $isModerator;
    protected $actionArr = array('comments' => array(), 'users' => array());
    protected $commentIdList = array();
    protected $userIdList = array();

    /**
     * Constructor.
     *
     * @param string $entityType
     * @param integer $entityId
     * @param integer $page
     * @param string $displayType
     */
    public function __construct( BASE_CommentsParams $params, $id, $page = 1 )
    {
        parent::__construct();
        $batchData = $params->getBatchData();
        $this->staticData = empty($batchData['_static']) ? array() : $batchData['_static'];
        $batchData = isset($batchData[$params->getEntityType()][$params->getEntityId()]) ? $batchData[$params->getEntityType()][$params->getEntityId()] : array();
        $this->params = $params;
        $this->batchData = $batchData;
        $this->id = $id;
        $this->page = $page;
        $this->isModerator = SC::getUser()->isAuthorized($params->getPluginKey());
        $this->isOwnerAuthorized = (SC::getUser()->isAuthenticated() && $this->params->getOwnerId() !== null && (int) $this->params->getOwnerId() === (int) SC::getUser()->getId());
        $this->isBaseModerator = SC::getUser()->isAuthorized('base');

        $this->commentService = BOL_CommentService::getInstance();
        $this->avatarService = BOL_AvatarService::getInstance();
        $this->cmpContextId = "comments-list-$id";
        $this->assign('cmpContext', $this->cmpContextId);

        $this->commentCount = isset($batchData['commentsCount']) ? $batchData['commentsCount'] : $this->commentService->findCommentCount($params->getEntityType(), $params->getEntityId());
        $this->init();
    }

    protected function processList( $commentList )
    {
        $arrayToAssign = array();

        /* @var $value BOL_Comment */
        foreach ( $commentList as $value )
        {
            $this->userIdList[] = $value->getUserId();
            $this->commentIdList[] = $value->getId();
        }

        $userAvatarArrayList = empty($this->staticData['avatars']) ? $this->avatarService->getDataForUserAvatars($this->userIdList) : $this->staticData['avatars'];

        /* @var $value BOL_Comment */
        foreach ( $commentList as $value )
        {
            $cmItemArray = array(
                'displayName' => $userAvatarArrayList[$value->getUserId()]['title'],
                'avatarUrl' => $userAvatarArrayList[$value->getUserId()]['src'],
                'profileUrl' => $userAvatarArrayList[$value->getUserId()]['url'],
                'content' => $value->getMessage(),
                'date' => UTIL_DateTime::formatDate($value->getCreateStamp()),
                'userId' => $value->getUserId(),
                'commentId' => $value->getId(),
                'avatar' => $userAvatarArrayList[$value->getUserId()],
            );

            $contentAdd = '';

            if ( $value->getAttachment() !== null )
            {
                $tempCmp = new BASE_CMP_OembedAttachment((array) json_decode($value->getAttachment()), $this->isOwnerAuthorized);
                $contentAdd .= '<div class="sc_attachment sc_small" id="att' . $value->getId() . '">' . $tempCmp->render() . '</div>';
            }

            $cmItemArray['content_add'] = $contentAdd;

            $event = new BASE_CLASS_EventProcessCommentItem('base.comment_item_process', $value, $cmItemArray);
            SC::getEventManager()->trigger($event);
            $arrayToAssign[] = $event->getDataArr();
        }

        return $arrayToAssign;
    }

    public function itemHandler( BASE_CLASS_EventProcessCommentItem $e )
    {
        $language = SC::getLanguage();

        $deleteButton = false;
        $cAction = null;
        $value = $e->getItem();

        if ( $this->isOwnerAuthorized || $this->isModerator || (int) SC::getUser()->getId() === (int) $value->getUserId() )
        {
            $deleteButton = true;
        }
        
        $flagButton = $value->getUserId() != SC::getUser()->getId();

        if ( $this->isBaseModerator || $deleteButton || $flagButton )
        {
            $cAction = new BASE_CMP_ContextAction();
            $parentAction = new BASE_ContextAction();
            $parentAction->setKey('parent');
            $parentAction->setClass('sc_comments_context');
            $cAction->addAction($parentAction);

            if ( $deleteButton )
            {
                $flagAction = new BASE_ContextAction();
                $flagAction->setLabel($language->text('base', 'contex_action_comment_delete_label'));
                $flagAction->setKey('udel');
                $flagAction->setParentKey($parentAction->getKey());
                $delId = 'del-' . $value->getId();
                $flagAction->setId($delId);
                $this->actionArr['comments'][$delId] = $value->getId();
                $cAction->addAction($flagAction);
            }

            if ( $this->isBaseModerator && $value->getUserId() != SC::getUser()->getId() )
            {
                $modAction = new BASE_ContextAction();
                $modAction->setLabel($language->text('base', 'contex_action_user_delete_label'));
                $modAction->setKey('cdel');
                $modAction->setParentKey($parentAction->getKey());
                $delId = 'udel-' . $value->getId();
                $modAction->setId($delId);
                $this->actionArr['users'][$delId] = $value->getUserId();
                $cAction->addAction($modAction);
            }
            
            if ( $flagButton )
            {
                $flagAction = new BASE_ContextAction();
                $flagAction->setLabel($language->text('base', 'flag'));
                $flagAction->setKey('cflag');
                $flagAction->setParentKey($parentAction->getKey());
                $flagAction->addAttribute("onclick", "var d = $(this).data(); SC.flagContent(d.etype, d.eid);");
                $flagAction->addAttribute("data-etype", "comment");
                $flagAction->addAttribute("data-eid", $value->id);

                $cAction->addAction($flagAction);
            }
        }

        if ( $this->params->getCommentPreviewMaxCharCount() > 0 && mb_strlen($value->getMessage()) > $this->params->getCommentPreviewMaxCharCount() )
        {
            $e->setDataProp('previewMaxChar', $this->params->getCommentPreviewMaxCharCount());
        }

        $e->setDataProp('cnxAction', empty($cAction) ? '' : $cAction->render());
    }

    protected function init()
    {
        if ( $this->commentCount === 0 && $this->params->getShowEmptyList() )
        {
            $this->assign('noComments', true);
        }

        $countToLoad = 0;

        if ( $this->commentCount === 0 )
        {
            $commentList = array();
        }
        else if ( in_array($this->params->getDisplayType(), array(BASE_CommentsParams::DISPLAY_TYPE_WITH_LOAD_LIST, BASE_CommentsParams::DISPLAY_TYPE_WITH_LOAD_LIST_MINI)) )
        {
            $commentList = empty($this->batchData['commentsList']) ? $this->commentService->findCommentList($this->params->getEntityType(), $this->params->getEntityId(), 1, $this->params->getInitialCommentsCount()) : $this->batchData['commentsList'];
            $commentList = array_reverse($commentList);
            $countToLoad = $this->commentCount - $this->params->getInitialCommentsCount();
            $this->assign('countToLoad', $countToLoad);
        }
        else
        {
            $commentList = $this->commentService->findCommentList($this->params->getEntityType(), $this->params->getEntityId(), $this->page, $this->params->getCommentCountOnPage());
        }

        SC::getEventManager()->trigger(new SC_Event('base.comment_list_prepare_data', array('list' => $commentList, 'entityType' => $this->params->getEntityType(), 'entityId' => $this->params->getEntityId())));
        SC::getEventManager()->bind('base.comment_item_process', array($this, 'itemHandler'));
        $this->assign('comments', $this->processList($commentList));
        $pages = false;

        if ( $this->params->getDisplayType() === BASE_CommentsParams::DISPLAY_TYPE_WITH_PAGING )
        {
            $pagesCount = $this->commentService->findCommentPageCount($this->params->getEntityType(), $this->params->getEntityId(), $this->params->getCommentCountOnPage());

            if ( $pagesCount > 1 )
            {
                $pages = $this->getPages($this->page, $pagesCount, 8);
                $this->assign('pages', $pages);
            }
        }
        else
        {
            $pagesCount = 0;
        }

        $this->assign('loadMoreLabel', SC::getLanguage()->text('base', 'comment_load_more_label'));

        static $dataInit = false;

        if ( !$dataInit )
        {
            $staticDataArray = array(
                'respondUrl' => SC::getRouter()->urlFor('BASE_CTRL_Comments', 'getCommentList'),
                'delUrl' => SC::getRouter()->urlFor('BASE_CTRL_Comments', 'deleteComment'),
                'delAtchUrl' => SC::getRouter()->urlFor('BASE_CTRL_Comments', 'deleteCommentAtatchment'),
                'delConfirmMsg' => SC::getLanguage()->text('base', 'comment_delete_confirm_message'),
                'preloaderImgUrl' => SC::getThemeManager()->getCurrentTheme()->getStaticImagesUrl() . 'ajax_preloader_button.gif'
            );
            SC::getDocument()->addOnloadScript("window.owCommentListCmps.staticData=" . json_encode($staticDataArray) . ";");
            $dataInit = true;
        }

        $jsParams = json_encode(
            array(
                'totalCount' => $this->commentCount,
                'contextId' => $this->cmpContextId,
                'displayType' => $this->params->getDisplayType(),
                'entityType' => $this->params->getEntityType(),
                'entityId' => $this->params->getEntityId(),
                'pagesCount' => $pagesCount,
                'initialCount' => $this->params->getInitialCommentsCount(),
                'loadMoreCount' => $this->params->getLoadMoreCount(),
                'commentIds' => $this->commentIdList,
                'pages' => $pages,
                'pluginKey' => $this->params->getPluginKey(),
                'ownerId' => $this->params->getOwnerId(),
                'commentCountOnPage' => $this->params->getCommentCountOnPage(),
                'cid' => $this->id,
                'actionArray' => $this->actionArr,
                'countToLoad' => $countToLoad
            )
        );

        SC::getDocument()->addOnloadScript(
            "window.owCommentListCmps.items['$this->id'] = new OwCommentsList($jsParams);
            window.owCommentListCmps.items['$this->id'].init();"
        );
    }

    protected function getPages( $currentPage, $pagesCount, $displayPagesCount )
    {
        $first = false;
        $last = false;

        $prev = ( $currentPage > 1 );
        $next = ( $currentPage < $pagesCount );

        if ( $pagesCount <= $displayPagesCount )
        {
            $start = 1;
            $displayPagesCount = $pagesCount;
        }
        else
        {
            $start = $currentPage - (int) floor($displayPagesCount / 2);

            if ( $start <= 1 )
            {
                $start = 1;
            }
            else
            {
                $first = true;
            }

            if ( ($start + $displayPagesCount - 1) < $pagesCount )
            {
                $last = true;
            }
            else
            {
                $start = $pagesCount - $displayPagesCount + 1;
            }
        }

        $pageArray = array();

        if ( $first )
        {
            $pageArray[] = array('label' => SC::getLanguage()->text('base', 'paging_label_first'), 'pageIndex' => 1);
        }

        if ( $prev )
        {
            $pageArray[] = array('label' => SC::getLanguage()->text('base', 'paging_label_prev'), 'pageIndex' => ($currentPage - 1));
        }

        if ( $first )
        {
            $pageArray[] = array('label' => '...');
        }

        for ( $i = (int) $start; $i <= ($start + $displayPagesCount - 1); $i++ )
        {
            $pageArray[] = array('label' => $i, 'pageIndex' => $i, 'active' => ( $i === (int) $currentPage ));
        }

        if ( $last )
        {
            $pageArray[] = array('label' => '...');
        }

        if ( $next )
        {
            $pageArray[] = array('label' => SC::getLanguage()->text('base', 'paging_label_next'), 'pageIndex' => ( $currentPage + 1 ));
        }

        if ( $last )
        {
            $pageArray[] = array('label' => SC::getLanguage()->text('base', 'paging_label_last'), 'pageIndex' => $pagesCount);
        }

        return $pageArray;
    }
}