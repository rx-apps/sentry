<?php
if (!defined('RX_BASEDIR')) {
	exit();
}

/** @var stdClass $lang */

/**
 * Basic Information.
 */
$lang->sentry_title = 'Sentry';
$lang->sentry_description = 'Sentry 에러 트래킹 서비스를 연결합니다.';

/**
 * 관리자 페이지 메뉴
 */
$lang->sentry_admin_menu_index = '대시보드';
$lang->sentry_admin_menu_test = '테스트 에러 생성';

/**
 * 관리자 페이지 대시보드
 */
$lang->sentry_admin_tit_check_update = '업데이트 확인';
$lang->sentry_admin_lbl_is_updated = '최신버전 여부';
$lang->sentry_admin_txt_already_updated = '최신버전을 사용하고 있습니다! (버전 %s)';
$lang->sentry_admin_txt_need_update = '최신버전으로 업데이트가 필요합니다! 깃헙에 방문하여 최신버전을 다운로드 받으세요. (버전 %s)';
$lang->sentry_admin_lbl_github_url = '깃헙 URL';
$lang->sentry_admin_txt_github_url = '네트워크 상태 불안정 등의 이유로 최신버전 정보가 부정확할 수 있습니다.<br />링크 접속 후 Watch -> Custom -> Releases 체크 후 Apply 버튼을 클릭하여 깃헙 알림센터를 통한 최신버전 알림을 받아볼 수 있습니다.';
$lang->sentry_admin_lbl_advertisement = '커피한잔 대신 (광고)';
$lang->sentry_admin_txt_advertisement_name = '어제 니 CSS 쩔더라ㅋ';
$lang->sentry_admin_txt_advertisement_url = 'https://smartstore.naver.com/dsticker/products/5945597066';

$lang->sentry_admin_tit_config = '기본 설정';
$lang->sentry_admin_lbl_dsn = 'Sentry DSN';
$lang->sentry_admin_txt_dsn = 'Settings > (Organization) Projects > 연동할 프로젝트 > (SDK Setup) Client Keys (DSN) 에서 확인할 수 있습니다.';
