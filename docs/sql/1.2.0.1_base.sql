ALTER TABLE relate_section_knowledge
 add create_user int not null default 0 comment '创建人',
 add delete_user int not null default 0 comment '删除人';