-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
CREATE TABLE F_MATERIAL_LINKAGE_TERRAFORM
(
ROW_ID                             %INT%                           , -- 識別シーケンス項番

MATERIAL_LINK_NAME                 %VARCHR%(128)                   ,
FILE_ID                            %INT%                           ,
CLOSE_REVISION_ID                  %INT%                           ,
TERRAFORM_MODULE_CHK               %INT%                           , -- Moduleファイル
TERRAFORM_POLICY_CHK               %INT%                           , -- Policyファイル

ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_MATERIAL_LINKAGE_TERRAFORM_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別

ROW_ID                             %INT%                           , -- 識別シーケンス項番

MATERIAL_LINK_NAME                 %VARCHR%(128)                    ,
FILE_ID                            %INT%                           ,
CLOSE_REVISION_ID                  %INT%                           ,
TERRAFORM_MODULE_CHK               %INT%                           , -- Moduleファイル
TERRAFORM_POLICY_CHK               %INT%                           , -- Policyファイル

ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
