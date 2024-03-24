<?php

namespace TYPO3\CMS\ContentBlocks\Backend\Preview;

use TYPO3\CMS\Backend\History\RecordHistory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ElementInformation
{
    public function __construct(
        protected readonly IconFactory $iconFactory,
    ) {}

    public function getVariables(array $row): array
    {
        $variables = [];
        $backendUser = $this->getBackendUser();
        $permsClause = $backendUser->getPagePermsClause(Permission::PAGE_SHOW);
        $uid = (int)$row['uid'];
        // Check permissions and uid value:
        $accessAllowed = false;
        if ($uid && $backendUser->check('tables_select', 'pages')) {
            $row = BackendUtility::readPageAccess($uid, $permsClause) ?: [];
            $accessAllowed = $row !== [];
        }
        $variables['extraFields'] = $this->getExtraFields($row);
        $variables['accessAllowed'] = $accessAllowed;
        $variables += $this->getPageTitle($row);
        $variables['maxTitleLength'] = $this->getBackendUser()->uc['titleLen'] ?? 20;
        return $variables;
    }

    protected function getExtraFields(array $row): array
    {
        $lang = $this->getLanguageService();
        $keyLabelPair['uid'] = [
            'value' => BackendUtility::getProcessedValueExtra('pages', 'uid', $row['uid']),
            'fieldLabel' => rtrim(htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:show_item.php.uid')), ':'),
        ];
        foreach (['crdate' => 'creationDate', 'tstamp' => 'timestamp'] as $field => $label) {
            if (isset($GLOBALS['TCA']['pages']['ctrl'][$field])) {
                $keyLabelPair[$field] = [
                    'value' => BackendUtility::datetime($row[$GLOBALS['TCA']['pages']['ctrl'][$field]]),
                    'fieldLabel' => rtrim(htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.' . $label)), ':'),
                    'isDatetime' => true,
                ];
            }
        }
        // Show the user who created the record
        $recordHistory = GeneralUtility::makeInstance(RecordHistory::class);
        $ownerInformation = $recordHistory->getCreationInformationForRecord('pages', $row);
        $ownerUid = (int)(is_array($ownerInformation) && $ownerInformation['usertype'] === 'BE' ? $ownerInformation['userid'] : 0);
        if ($ownerUid) {
            $creatorRecord = BackendUtility::getRecord('be_users', $ownerUid);
            if ($creatorRecord) {
                $keyLabelPair['creatorRecord'] = [
                    'value' => $creatorRecord,
                    'fieldLabel' => rtrim(htmlspecialchars($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.creationUserId')), ':'),
                ];
            }
        }
        return $keyLabelPair;
    }

    /**
     * Get page title with icon, table title and record title
     */
    protected function getPageTitle(array $row): array
    {
        $pageTitle = [
            'title' => BackendUtility::getRecordTitle('pages', $row),
            'table' => $this->getLanguageService()->sL($GLOBALS['TCA']['pages']['ctrl']['title']),
            'icon' => $this->iconFactory->getIconForRecord('pages', $row, Icon::SIZE_SMALL),
        ];
        return $pageTitle;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
