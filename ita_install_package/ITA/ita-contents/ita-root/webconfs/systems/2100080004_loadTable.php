<?php
//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//    ・作業パターン一覧画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102710");
/*
Terraform作業パターン
*/
    $tmpAry = array(
        'TT_SYS_01_JNL_SEQ_ID'=>'JOURNAL_SEQ_NO',
        'TT_SYS_02_JNL_TIME_ID'=>'JOURNAL_REG_DATETIME',
        'TT_SYS_03_JNL_CLASS_ID'=>'JOURNAL_ACTION_CLASS',
        'TT_SYS_04_NOTE_ID'=>'NOTE',
        'TT_SYS_04_DISUSE_FLAG_ID'=>'DISUSE_FLAG',
        'TT_SYS_05_LUP_TIME_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TT_SYS_06_LUP_USER_ID'=>'LAST_UPDATE_USER',
        'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'=>'ROW_EDIT_BY_FILE',
        'TT_SYS_NDB_UPDATE_ID'=>'WEB_BUTTON_UPDATE',
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP'
    );

    $table = new TableControlAgent('E_TERRAFORM_PATTERN','PATTERN_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102720"), 'E_TERRAFORM_PATTERN_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['PATTERN_ID']->setSequenceID('C_PATTERN_PER_ORCH_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_PATTERN_PER_ORCH_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('C_PATTERN_PER_ORCH');
    $table->setDBJournalTableHiddenID('C_PATTERN_PER_ORCH_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    //----作業実行で必要なのでtrueに
    $table->setJsEventNamePrefix(true);
    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102730"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102740"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $table->addUniqueColumnSet(array('ITA_EXT_STM_ID','PATTERN_NAME'));

    //************************************************************************************
    //----作業パターン名
    //************************************************************************************
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('PATTERN_NAME',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102750"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102760"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
            $exeQueryData[$objColumn->getID()] = 10;
        }else if( $modeValue=="DTUP_singleRecDelete" ){
            $exeQueryData[$objColumn->getID()] = 10;
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };

    //************************************************************************************
    //----オーケストレータ
    //************************************************************************************
    $c = new IDColumn('ITA_EXT_STM_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102770"),'B_ITA_EXT_STM_MASTER','ITA_EXT_STM_ID','ITA_EXT_STM_NAME','B_ITA_EXT_STM_MASTER');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102780"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->setJournalTableOfMaster('B_ITA_EXT_STM_MASTER_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('ITA_EXT_STM_ID');
    $c->setJournalDispIDOfMaster('ITA_EXT_STM_NAME');
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);
    $table->addColumn($c);

    //************************************************************************************
    //----遅延タイマー
    //************************************************************************************
    $c = new NumColumn('TIME_LIMIT',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102790"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102800"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(null,null));
    $table->addColumn($c);

    //************************************************************************************
    //----Terraform利用情報
    //************************************************************************************
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102810"));
        $c = new IDColumn('TERRAFORM_WORKSPACE_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102820"),'D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK','WORKSPACE_ID','ORGANIZATION_WORKSPACE','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102830"));//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
        $c->setJournalTableOfMaster('D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('WORKSPACE_ID');
        $c->setJournalDispIDOfMaster('ORGANIZATION_WORKSPACE');
        $c->setRequired(true);//登録/更新時には、入力必須
        $cg->addColumn($c);
    $table->addColumn($cg);

    $table->fixColumn();
    $tmpAryColumn = $table->getColumns();
    list($strTmpValue,$tmpKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('callType'),null);
    if( $tmpKeyExists===true ){
        if( $strTmpValue=="insConstruct" ){
            $objRadioColumn = $tmpAryColumn['WEB_BUTTON_UPDATE'];
            $objRadioColumn->setColLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102840"));
            
            $objFunctionB = function ($objOutputType, $rowData, $aryVariant, $objColumn){
                $strInitedColId = $objColumn->getID();
                
                $aryVariant['callerClass'] = get_class($objOutputType);
                $aryVariant['callerVars'] = array('initedColumnID'=>$strInitedColId,'free'=>null);
                $strRIColId = $objColumn->getTable()->getRIColumnID();
                
                $rowData[$strInitedColId] = '<input type="radio" name="patternNo" onclick="javascript:patternLoadForExecute(' . $rowData[$strRIColId] . ')"/>';
                
                return $objOutputType->getBody()->getData($rowData,$aryVariant);
            };
            
            $objTTBF = new TextTabBFmt();
            $objTTHF = new TabHFmt();//new SortedTabHFmt();
            $objTTBF->setSafingHtmlBeforePrintAgent(false);
            $objOutputType = new VariantOutputType($objTTHF, $objTTBF);
            $objOutputType->setFunctionForGetBodyTag($objFunctionB);
            $objOutputType->setVisible(true);
            $objRadioColumn->setOutputType("print_table", $objOutputType);
            
            $table->getFormatter('print_table')->setGeneValue("linkExcelHidden",true);
            $table->getFormatter('print_table')->setGeneValue("linkCSVFormShow",false);
        }
    }
    unset($tmpAryColumn);

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
