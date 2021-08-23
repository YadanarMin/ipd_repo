<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get("/",function(){
    return view('login');
});

Route::get('/login', 'LoginController@index');
Route::post('/login/checklogin', 'LoginController@checklogin');
Route::get('/login/successlogin', 'LoginController@successlogin');
Route::get('/login/logout', 'LoginController@logout');
Route::post('/change/newpassword','LoginController@ChangePassword');   
Route::get('/change/password','LoginController@NewPasswordIndex');
Route::get('/login/create/step1','LoginController@LoadAccountCreationgStep1');
Route::get('/login/create/step2','LoginController@LoadAccountCreationgStep2');
Route::get('/login/create/step3','LoginController@LoadAccountCreationgStep3');
Route::post('/login/email','LoginController@SendingEmail');
Route::post('/login/account/save','LoginController@SaveData');
Route::post('/login/account/get','LoginController@GetPersonalInfo');
Route::get('/login/approve/step1/{user_id}','LoginController@LoadApprovalByChiefAdmin');
Route::get('/login/approve/step2/{user_id}/{chief_admin_id}','LoginController@LoadApprovalByCCCAdmin');
Route::post('/login/account/delete','LoginController@DeleteLoginAccountInfo');

Route::group(['middleware' => 'guest'], function() {
    //Route::get('/forge/index','ForgeController@index');
    
    Route::get('/forge/callback','ForgeController@ForgeCallBack');
    Route::post('/forge/login', 'ForgeController@GetThreeLeggedToken');
    Route::get('/forge/index','ForgeController@index');
    Route::post('/forge/getData','ForgeController@GetData');
    Route::post('/forge/saveData','ForgeController@SaveData');
    Route::get('/forge/volumeChart','ForgeController@ShowVolumePage');
    Route::get('/forge/tekkin','ForgeController@ShowTekkin');
    Route::get('/forge/tekkintest','ForgeController@ShowTekkintest');
    Route::get('/forge/excelDownload','ForgeController@ExcelDownload');
    Route::get('/forge/excelDownloadKouji','ForgeController@KoujiExcelDownload');
    Route::get('/forge/excelDownloadMngBIM','ForgeController@MngBIMExcelDownload');
    Route::get('/forge/wordDownloadImplementation','ForgeController@ImplementationWordDownload');
    Route::get('/forge/doorWindowDetail','ForgeController@DoorWindowPageLoad');
    
    //To get Ashiba and Kasetsu Model 
    Route::post('/ashiba/getData','AshibaController@GetData');
    Route::post('/kasetsu/getData', 'AshibaController@GetKasetsuData');
    
    Route::get('/forge/info','AshibaController@ShowProjectInfo');
    
    Route::get('/admin/index','AdminController@index');
    Route::get('/admin/backup','AdminController@backup');
    
    Route::get('/user/index','LoginController@loginUser');
    Route::get('/user/authoritySettings','LoginController@authoritySettings');
    Route::post('/user/create','LoginController@SaveLoginUserInfo');
    Route::post('/user/createAuthority','LoginController@CreateAuthorityInfo');
    Route::post('/user/updateAuthority','LoginController@UpdateAuthorityInfo');
    Route::post('/user/updateAllAuthority','LoginController@UpdateAllAuthorityInfo');
    Route::post('/user/getData','LoginController@GetData');
    Route::post('/user/getAuthorityData','LoginController@GetAuthorityData');
    Route::post('/user/deleteData','LoginController@DeleteData');
    Route::post('/user/deleteAuthorityData','LoginController@DeleteAuthorityData');
    Route::post('/user/getAllUserData','LoginController@GetAllUserData');
    Route::post('/user/getContents','LoginController@GetContents');
    Route::post('/user/setContents','LoginController@SetContents');
    Route::post('/user/deleteContents','LoginController@DeleteContent');
    Route::post('/user/getAccessHistory','LoginController@GetAccessHistory');
    Route::post('/user/setAccessHistory','LoginController@SetAccessHistory');
    Route::post('/user/changeSetting','LoginController@ChangeLoginUserSetting');
    
    Route::get('/roomProp/index','RoomPropController@index');
    Route::post('/roomProp/getRoomProp','RoomPropController@GetRoomProp');
    
    Route::get('/dataPortal/index','DataPortalController@index');
    Route::get('/dataPortal/projectOverview','DataPortalController@ShowProjectOverview');
    Route::get('/dataPortal/projectSearchConsole','DataPortalController@ShowProjectSearchConsole');
    Route::get('/dataPortal/roomInfoSearchConsole','DataPortalController@ShowRoomInfoSearchConsole');
    Route::get('/dataPortal/tekkinVolumeOverview','DataPortalController@ShowTekkinVolumeOverview');
    Route::get('/dataPortaltest/projectSearchConsole','DataPortalController@ShowProjectSearchConsoletest'); //[test]
    
    Route::get('/forge/room','ForgeController@ShowRoom');
    
    Route::get('/prjmgt/index','ProjectMgtController@index');
    Route::post('/prjmgt/saveData','ProjectMgtController@saveData');
    Route::post('/prjmgt/getData','ProjectMgtController@getData');
    Route::post('/prjmgt/deleteData','ProjectMgtController@deleteData');
    
    Route::get('/crane/search','CraneController@DisplaySearchPage');
    Route::get('/crane/save','CraneController@DisplaySavePage');
    
    Route::get('/common/changedInfo','CommonController@DisplayChangedInfoPage');
    Route::get('/common/saveRoom','CommonController@DisplaySaveRoomPage');
    Route::get('/common/userInfo','CommonController@DisplayuserInfoPage');
    Route::get('/common/accessLog','CommonController@DisplayAccessLogPage');
    Route::post('/common/saveAccessLog','CommonController@SaveAccessLog');
    Route::post('/common/getAccessLog','CommonController@GetAccessLog');
    Route::get('/common/upload','CommonController@UploadFilesIndex');
    Route::post('/common/uploadFiles','CommonController@UploadFiles');
    Route::get('/common/download/{files}','CommonController@DownloadFiles');
    Route::post('/common/updateAllstore','CommonController@UpdateAllStoreFromBrowser');
    
     //Route::post('/common/uploadCapture','CommonController@UploadCapture');
    //Route::get('/common/uploadImagesIndex','CommonController@UploadImagesIndex');
    //Route::post('/common/uploadImages','CommonController@UploadReportImage');
    //Route::post('/common/getImages','CommonController@GetImagesByProjectCode');
    //Route::post('/common/getData','CommonController@GetData');
    //Route::post('/common/boxUpload','CommonController@UploadFileToBox');
    Route::get('/project/hashtag','ProjectReportController@Loadhashtag');
    Route::get('/project/report/{pj_code?}/{search_states?}','ProjectReportController@LoadPage');
    Route::get('/project/temp/{pj_code?}/{search_states?}','ProjectReportController@LoadTempPage');
    Route::post('/report/save','ProjectReportController@SaveInfo');
    Route::get('/report/uploadImagesIndex','ProjectReportController@UploadImagesIndex');
    Route::post('/report/uploadImages','ProjectReportController@UploadReportImage');
    Route::post('/report/uploadCapture','ProjectReportController@UploadCapture');
    Route::post('/report/getImages','ProjectReportController@GetImagesByProjectCode');
    Route::post('/report/getData','ProjectReportController@GetData');
    Route::post('/report/boxUpload','ProjectReportController@UploadFileToBox');
    Route::post('/report/deleteCapture','ProjectReportController@DeleteCapture');
    Route::post('/report/getReportImage','ProjectReportController@GetReportImageFromBox');
    Route::post('/report/renameCapture','ProjectReportController@RenameCapture');
    
    Route::get('/forge/property','ForgeController@GetRoomProperties');
    
    Route::get('/admin/savePro','AdminController@GetForgeProperties');
    Route::get('/admin/pageDescription','AdminController@LoadDescriptionPage');
    Route::post('/admin/UploadImages','AdminController@UploadImages');//upload file or images to a UPLOAD temporary folder
    Route::post('/admin/setToSession','AdminController@SetPageNameToSession');
    Route::get('/admin/fileDownload','AdminController@FileDownload');
    Route::get('/login/saveRoom','LoginController@GetRoomProperties');
    
    //Route::get('/OBJ/Convert','OBJController@ChangeOBJ');
    Route::get('/OBJ/index','OBJController@ShowPage');
    Route::get('/OBJ/Convert/{fileName}','OBJController@ChangeOBJ');
    //Route::post('/OBJ/Convert','OBJController@ChangeOBJ2');
    Route::post('/OBJ/upload','OBJController@Upload');
    
    Route::get('/Test/User','Bim360Users@User');
    
    Route::post('/bim360/setProjectIdToSession','Bim360UserController@SetProjectIdToSession');//ShowPermissionPage
    Route::get('/bim360/permission','Bim360UserController@ShowPage');
    Route::post('/bim360/getPermissionData','Bim360UserController@GetPermissionData');
    Route::post('/bim360/getUsers','Bim360UserController@GetAllUsers');
    Route::post('/bim360/managePermission','Bim360UserController@PermissionManagement');
    Route::get('/bim360/index','Bim360UserController@Index');
    Route::get('/bim360/test','Bim360UserController@Add');
    Route::get('/bim360/getTwoLeggedToken','Bim360UserController@GetTwoLeggedToken');
    
    
    Route::get('/admin/test','AdminController@GetForgeProperties');
    
    Route::get('/projectsave/test','PrjSaveController@GetForgeProperties');
    
    Route::get('/gantt/index','GanttChartController@index');
    Route::get('/gantt/processControll','GanttChartController@processControll');
    Route::post('/gantt/putData','GanttChartController@putData');
    Route::post('/gantt/getData','GanttChartController@getData');
    Route::post('/gantt/setProjectIdToSession','GanttChartController@SetProjectIdToSession');
    Route::get('/gantt/sampletest','GanttChartController@indextest');               //[test]
    
    Route::get('/allstore/index','AllstoreController@index');
    Route::post('/allstore/getData','AllstoreController@GetData');
    Route::post('/allstore/getDataByPjCode','AllstoreController@GetDataByPjCode');
    Route::post('/allstore/saveData','AllstoreController@SaveData');
    Route::post('/allstore/deleteData','AllstoreController@DeleteData');
    Route::post('/allstore/updateFlag','AllstoreController@UpdateDisplayReportFlag');
    Route::get('/allstore/excelDownloadBIMmane','AllstoreController@BIMmaneExcelDownload');
    Route::get('/allstore/excelDownloadfaciBIM','AllstoreController@FaciBIMExcelDownload');
    Route::post('/allstore/getBOXData','AllstoreController@UpdateLatestBOXData');
    Route::post('/allstore/recordHistory','AllstoreController@RecordAllstoreUpdateHistory');
    Route::post('/allstore/getHistory','AllstoreController@getAllstoreUpdateHistory');
    Route::post('/allstore/getRecordNum','AllstoreController@GetRecordNum');

    Route::get('/processMapping/index','ProcessMappingController@index');
    Route::get('/processMapping/excelDownloadProcess','ProcessMappingController@ProcessExcelDownload');
    Route::post('/processMapping/getData','ProcessMappingController@GetData');
    Route::post('/processMapping/saveData','ProcessMappingController@SaveData');
    Route::post('/processMapping/updateData','ProcessMappingController@UpdateData');
    Route::post('/processMapping/deleteData','ProcessMappingController@DeleteData');
    
    Route::get('/pdf/{pdfName}','AdminController@ShowPDF');

    Route::get('/addin/calcAshiba','AddinController@LoadCalcAshiba');
    Route::get('/addin/calcKasetsu','AddinController@LoadCalcKasetsu');
    
    Route::get('/document/management','DocumentController@index');
    Route::get('/document/managementTest','DocumentController@indexTest');
    Route::get('/document/templateConsole','DocumentController@templateConsole');
    Route::get('/document/downloadConsole','DocumentController@downloadConsole');
    Route::get('/document/outputExcelTemplate/{name}','DocumentController@outputExcelTemplate');
    Route::get('/document/outputDefaultExcelTemplate/{name}','DocumentController@outputDefaultExcelTemplate');
    Route::post('/document/saveData','DocumentController@SaveTemplateData');
    Route::post('/document/getData','DocumentController@GetTemplateData');
    Route::post('/document/deleteData','DocumentController@DeleteTemplate');
    Route::get('/document/templateConsoleWord','DocumentController@templateConsoleWord');
    Route::get('/document/downloadConsoleWord','DocumentController@downloadConsoleWord');
    Route::get('/document/outputWordTemplate/{name}','DocumentController@outputWordTemplate');
    Route::get('/document/outputDefaultWordTemplate/{name}','DocumentController@outputDefaultWordTemplate');
    Route::post('/document/saveWordData','DocumentController@SaveWordTemplateData');
    Route::post('/document/getWordData','DocumentController@GetWordTemplateData');
    Route::post('/document/deleteWordData','DocumentController@DeleteWordTemplate');
    
    //留学生情報画面
    Route::get('/foreignStudents/index','ForeignStudentsController@index');
    Route::get('/foreignStudents/show','ForeignStudentsController@ShowData');
    Route::get('/foreignStudents/insert','ForeignStudentsController@InsertPage');
    Route::post('/foreignStudents/insertData','ForeignStudentsController@InsertData');
    Route::post('/foreignStudents/getData','ForeignStudentsController@GetData');
    Route::post('/foreignStudents/saveData','ForeignStudentsController@SaveData');
    Route::post('/foreignStudents/deleteData','ForeignStudentsController@DeleteData');
    Route::get('/foreignStudents/compare/{id}','ForeignStudentsController@CompareData');
    Route::post('/foreignStudents/compare','ForeignStudentsController@GetCompareData');
    Route::post('/foreignStudents/updateData','ForeignStudentsController@UpdateData');
    
    //協力会社管理画面
    Route::get('/partnerCompany/index', 'PartnerCompanyController@index');
    Route::post('/partnerCompany/saveData', 'PartnerCompanyController@SaveData');
    Route::get('/partnerCompany/list', 'PartnerCompanyController@ShowList');
    Route::post('/partnerCompany/getData', 'PartnerCompanyController@GetData');
    Route::post('/partnerCompany/deleteData', 'PartnerCompanyController@DeleteData');
    Route::post('/partnerCompany/updateData', 'PartnerCompanyController@UpdateData');
    
    //モデリング会社管理画面
    Route::get('/modellingCompany/index', 'ModellingCompanyController@index');
    Route::post('/modellingCompany/saveData', 'ModellingCompanyController@SaveData');
    Route::post('/modellingCompany/getData', 'ModellingCompanyController@GetData');
    Route::post('/modellingCompany/deleteData', 'ModellingCompanyController@deleteData');
    Route::get('/modellingCompany/list', 'ModellingCompanyController@ShowList');
    Route::post('/modellingCompany/updateData', 'ModellingCompanyController@UpdateData');
    
    //各種申し込む画面
    Route::get('/application/index', 'ApplicationController@index');
    Route::get('/application/insert', 'ApplicationController@InsertPage');
    Route::get('/application/insertUpdate/{date}', 'ApplicationController@InsertPageWithDate');
    Route::get('/application/edit', 'ApplicationController@EditPage');
    Route::get('/application/edit/{id}', 'ApplicationController@EditUserInfo');
    Route::post('/application/saveInsertData', 'ApplicationController@SaveInsertData');
    Route::get('/application/insert/page2', 'ApplicationController@InsertPage2');
    Route::get('/application/insert/page3', 'ApplicationController@InsertPage3');
    Route::post('/application/getData', 'ApplicationController@GetData');
    Route::post('/application/getData/courseInfo', 'ApplicationController@GetCourseInfo');
    Route::post('/application/deleteData', 'ApplicationController@DeleteData');
    Route::post('/application/updateData', 'ApplicationController@UpdateData1');
    
    //申し込む内容確認画面
    Route::get('/applicationConfirm/index', 'ApplicationController@ConfirmPage');
    Route::get('/applicationConfirm/edit', 'ApplicationController@EditPage2');
    Route::post('/applicationConfirm/update', 'ApplicationController@UpdateData');
    
    //見積管理画面
    Route::get('/estimate/index', 'EstimateController@index');
    Route::get('/estimate/upload', 'EstimateController@UploadPage');
    Route::get('/estimate/project/{ipdCode?}', 'EstimateController@ProjectSelect');
    Route::post('/estimate/getData', 'EstimateController@GetData');
    Route::get('/estimateSetting/index/{newTab?}', 'EstimateController@SettingView');
    Route::post('/estimate/createFolder', 'EstimateController@CreateFolder');
    Route::post('/estimate/check', 'EstimateController@CheckBoxFolder');
    Route::post('/estimate/updateData', 'EstimateController@UpdateData');
    
    //Hashtag Search画面
    Route::get('/hashtag/index', 'HashtagController@index');
    Route::post('/hashtag/getData', 'HashtagController@GetData');
    
    //Text-editor
    Route::get('/project/test/{pj_code?}/{search_states?}','ProjectReportController@Test');
    Route::get('/textEditor/index', 'HashtagController@Test');
    
    
    //Excel Export Testing
    Route::get('/application/excelDownload', 'ApplicationController@ExcelExport');
    
    Route::get('/correlation/index','CorrelationController@index');
    Route::get('/homeDesign/index','CorrelationController@sample');

    Route::get('/customDocument/index','CustomDocumentCreationController@LoadPage');
    Route::post('/customDocument/save','CustomDocumentCreationController@Save');
    Route::post('/customDocument/getData','CustomDocumentCreationController@GetData');
    
    Route::get('/projectAccessSetting/index','ProjectAccessSettingController@index');
    Route::get('/projectAccessSetting/setting/{access_id}/{access_name}','ProjectAccessSettingController@LoadSettingPage');
    Route::get('/projectAccessSetting/authoritySet/{access_id?}/{access_name?}/{setId?}','ProjectAccessSettingController@LoadAuthoritySetPage');
    Route::get('/projectAccessSetting/authorityItemSet/{access_id?}/{access_name?}/{setId?}','ProjectAccessSettingController@LoadAuthorityItemSetPage');
    Route::get('/projectAccessSetting/modelDataSet/{access_id}/{access_name}/{setId?}','ProjectAccessSettingController@LoadAuthorityModelDataSetPage');
    Route::post('/projectAccessSetting/saveData','ProjectAccessSettingController@SaveData');
    Route::post('/projectAccessSetting/getData','ProjectAccessSettingController@GetData');
    Route::post('/projectAccessSetting/deleteData','ProjectAccessSettingController@DeleteData');
    
    Route::get('/personal/index','PersonalController@index');
    Route::post('/personal/getData','PersonalController@GetData');
    Route::post('/personal/getUser','PersonalController@GetUser');
    
    Route::get('/company/index/{is_other_page?}','CompanyController@index');
    Route::post('/company/saveType','CompanyController@SaveCompanyType');
    Route::post('/company/getType','CompanyController@GetCompanyType');
    Route::post('/company/getBranch','CompanyController@GetCompanyBranch');
    Route::post('/company/saveData','CompanyController@SaveData');
    Route::post('/company/getData','CompanyController@GetData');
    Route::post('/company/deleteData','CompanyController@DeleteData');
    Route::post('/company/deleteBranch','CompanyController@DeleteBranchData');

    Route::get('/personalInsert/index','PersonalInsertController@index');
    Route::post('/personalInsert/saveData','PersonalInsertController@SaveData');
    Route::post('/personalInsert/deleteData','PersonalInsertController@DeleteData');
    
});

Route::post('/box/login','CommonController@BoxLogin');
Route::post('/box/getData','CommonController@GetBoxData');
Route::get('/box/callback','CommonController@BoxCallBack');


Route::get('/test/autosave','LoginController@GetForgeProperties');
Route::get('/test/autosaveProject','CraneController@GetForgeProjects');
Route::get('/test/autobackup','LoginController@AutoBackup');
Route::get('/test/saveroom','LoginController@GetRoomProperties');
Route::get('/test/bim360','Bim360UserController@User');
Route::get('/test/mail','CustomDocumentCreationController@SendMail');


