-- 创建数据库
create database shop charset utf8;
use shop;

-- 创建表
-- 表前缀sh_
create table sh_admin(
a_id int not null primary key auto_increment,
a_username varchar(10) not null comment '用户名',
a_password char(50) not null comment '用户密码，md5加密',
a_last_log_ip char(15) comment '上次登录ip',
a_last_log_time int unsigned not null comment '用户上次登录时间'
)charset utf8 engine = innodb;

-- 插入一个用户
insert into sh_admin values(null,'admin',md5('admin'),'',1);

select a_id  id,a_username uname,a_password apass,a_last_log_ip alip,a_last_log_time altime from sh_admin;


-- 创建商品分类表
create table sh_category(
c_id int not null primary key auto_increment,
c_name varchar(20) not null comment '商品分类的名称',
c_inv int unsigned not null default 0 comment '商品分类对应商品的数量',
c_sort int default 50 comment '商品分类的排序',
c_parent_id int not null default 0 comment '商品分类的父级ID，0表示顶级分类'
)charset utf8 engine = innodb;

-- 插入数据
insert into sh_category values
(1,'手机',default,default,default),
(2,'双模手机',default,default,1),
(3,'3G手机',default,default,1),
(4,'CDMA手机',default,default,1),
(5,'手机配件',default,default,default),
(6,'电池',default,default,5),
(7,'充电器',default,default,5),
(8,'耳机',default,default,5),
(9,'beats耳机',default,default,8),
(10,'电视',default,default,default),
(11,'液晶电视',default,default,10),
(12,'等离子电视',default,default,10),
(13,'平板电视',default,default,10),
(14,'手机外壳',default,default,5);


-- 创建商品表
create table sh_goods(
g_id int not null primary key auto_increment,
g_name varchar(20) not null comment '商品名称',
g_desc text comment '商品描述',
g_sn char(10) not null comment '商品货号',
g_price decimal(10,2) default 1.0 comment '商品价格',
g_inv int unsigned not null default 0 comment '商品库存',
g_sort int default 50 comment '商品排序',
c_id int not null comment '商品分类',
g_is_sale tinyint default 1 comment '商品是否上架，1表示上架，0表示下架',
g_img varchar(255) comment '商品图片路径',
g_thumb_img varchar(255) comment '商品缩略图路径',
g_water_img varchar(255) comment '商品水印图',
g_is_hot tinyint default 0 comment '商品是否热销，0默认不热销',
g_is_new tinyint default 1 comment '商品是否是新品，1默认是新品',
g_is_pro tinyint default 0 comment '商品是否促销，0默认不促销'
)charset utf8 engine = innodb;

-- 插入数据
insert into sh_goods values
(null,'IPHONE6','史上最好手机','GOODS00001',5288,0,default,4,default,'','','',default,default,default),
(null,'IPHONE5','史上次好手机','GOODS00002',5288,0,default,4,default,'','','',default,default,default),
(null,'IPHONE4S','最好手机','GOODS00003',5288,0,default,4,default,'','','',default,default,default),
(null,'IPHONE4','不错手机','GOODS00004',4288,0,default,4,default,'','','',default,default,default),
(null,'Galaxy S5','Samsung手机','GOODS00005',5488,0,default,3,default,'','','',default,default,default),
(null,'飞毛腿','移动电源','GOODS00006',5288,0,default,6,default,'','','',default,default,default),
(null,'长虹','电视机','GOODS00007',5288,0,default,12,default,'','','',default,default,default),
(null,'索尼','手机','GOODS00008',5288,0,default,4,default,'','','',default,default,default),
(null,'LG液晶电视','液晶电视','GOODS00009',5288,0,default,11,default,'','','',default,default,default),
(null,'三星电视','三星','GOODS00010',5288,0,default,11,default,'','','',default,default,default),
(null,'苹果电视','苹果','GOODS00011',5288,0,default,11,default,'','','',default,default,default),
(null,'创维','口碑不错的电视','GOODS00012',5288,0,default,13,default,'','','',default,default,default),
(null,'康佳','比较悠久的电视','GOODS00013',5288,0,default,13,default,'','','',default,default,default),
(null,'诺基亚','已经过气的手机','GOODS00014',5288,0,default,2,default,'','','',default,default,default),
(null,'夏普','女士手机','GOODS00015',5288,0,default,2,default,'','','',default,default,default),
(null,'海尔','可外接的电视','GOODS00016',5288,0,default,12,default,'','','',default,default,default)
;
