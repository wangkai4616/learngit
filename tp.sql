-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2019-12-24 11:43:27
-- 服务器版本： 8.0.12
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `tp`
--

-- --------------------------------------------------------

--
-- 表的结构 `admins`
--

CREATE TABLE `admins` (
  `id` int(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(32) NOT NULL,
  `truename` varchar(20) NOT NULL,
  `cid` tinyint(10) NOT NULL,
  `status` int(2) UNSIGNED ZEROFILL NOT NULL,
  `add_time` datetime NOT NULL,
  `rights` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限列表',
  `admin_img` varchar(80) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL DEFAULT '' COMMENT '用户头像'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `truename`, `cid`, `status`, `add_time`, `rights`, `admin_img`) VALUES
(1, 'admin', 'a66abb5684c45962d887564f08346e8d', '王文博', 1, 00, '2019-10-20 15:49:31', '[1,5,7,10,11,12,13,2,3,4,14,15,16,17,18,19,8,9]', '/static/admin_img/20191223\\cb471908c87201d3fb67ed97963aee3d.jpg'),
(2, 'liuxiaolian', '64df9d9d65bd23262640510047ad9b9a', '刘小脸', 2, 00, '2019-12-21 16:37:01', '[1,2,3,4,5,6,7,8,9,10,11,12,13]', '/static/admin_img/20191223\\111262a2002c5c643c763a085d5e445d.jpg'),
(3, 'liqin', '83abd1430acf3deae2265958784fc540', '李沁', 3, 01, '2019-12-21 16:37:44', '[1,5,2,3,4,9]', '/static/admin_img/20191223\\111262a2002c5c643c763a085d5e445d.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `admin_company`
--

CREATE TABLE `admin_company` (
  `cid` int(10) NOT NULL COMMENT 'ID',
  `company` varchar(10) NOT NULL COMMENT '社名',
  `gid` int(10) NOT NULL COMMENT '所属类别',
  `add_time` int(20) NOT NULL COMMENT '添加时间',
  `fid` int(10) NOT NULL COMMENT '结算财务表'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `admin_company`
--

INSERT INTO `admin_company` (`cid`, `company`, `gid`, `add_time`, `fid`) VALUES
(1, '超级管理', 1, 1576844750, 1),
(2, '康辉国际旅行社', 2, 1576844750, 2),
(3, '西玛国际旅行社', 2, 1576844750, 3),
(4, '九洲商务旅行社', 3, 1576844750, 4);

-- --------------------------------------------------------

--
-- 表的结构 `admin_groups`
--

CREATE TABLE `admin_groups` (
  `gid` int(11) NOT NULL COMMENT '角色ID',
  `title` varchar(20) NOT NULL COMMENT '角色名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `admin_groups`
--

INSERT INTO `admin_groups` (`gid`, `title`) VALUES
(1, '超级管理员'),
(2, '组团社'),
(3, '地接社');

-- --------------------------------------------------------

--
-- 表的结构 `admin_menus`
--

CREATE TABLE `admin_menus` (
  `mid` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `pid` int(10) UNSIGNED NOT NULL COMMENT '    ',
  `ord` int(10) UNSIGNED ZEROFILL NOT NULL COMMENT '排序',
  `title` varchar(40) NOT NULL COMMENT '菜单名称',
  `controller` varchar(10) NOT NULL COMMENT '控制器名称',
  `method` varchar(30) NOT NULL COMMENT '方法名',
  `ishidden` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
  `status` tinyint(2) UNSIGNED ZEROFILL NOT NULL COMMENT '状态',
  `title_icon` varchar(255) DEFAULT NULL COMMENT '菜单图标'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `admin_menus`
--

INSERT INTO `admin_menus` (`mid`, `pid`, `ord`, `title`, `controller`, `method`, `ishidden`, `status`, `title_icon`) VALUES
(1, 0, 0000000000, '订单管理', '', '', 00, 00, 'layui-icon-form'),
(2, 0, 0000000000, '财务管理', '', '', 00, 00, 'layui-icon-rmb'),
(3, 0, 0000000000, '数据分析', '', '', 00, 00, 'layui-icon-chart\r\n'),
(4, 0, 0000000000, '人员管理', '', '', 00, 00, 'layui-icon-user'),
(5, 1, 0000000000, '创建订单', 'User', 'user_create', 00, 00, NULL),
(7, 1, 0000000000, '订单列表', 'User', 'user_list', 00, 00, ''),
(8, 0, 0000000000, '后台首页', 'Home', 'index', 01, 00, NULL),
(9, 0, 0000000001, '欢迎', 'Home', 'welcome', 00, 00, 'layui-icon-home'),
(10, 1, 0000000000, '保存基本信息', 'User', 'user_storage', 01, 00, NULL),
(11, 1, 0000000000, '获取订单列表', 'User', 'user_list_info', 01, 00, NULL),
(12, 1, 0000000000, '获取订单详细信息', 'User', 'order_show', 01, 00, NULL),
(13, 1, 0000000000, '订单删除', 'User', 'order_del', 01, 00, NULL),
(14, 4, 0000000000, '员工列表', 'Admin', 'admin_list', 00, 00, NULL),
(15, 4, 0000000000, '员工管理', 'Admin', 'admin_manage', 01, 00, NULL),
(16, 4, 0000000000, '账号管理', 'Admin', 'admin_account', 00, 00, NULL),
(17, 4, 0000000000, '权限保存', 'Admin', 'admin_manage_preserve', 01, 00, NULL),
(18, 4, 0000000000, '头像上传', 'Admin', 'admin_img', 01, 00, NULL),
(19, 4, 0000000000, '账号信息保存', 'Admin', 'admin_change', 01, 00, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(20) NOT NULL COMMENT '行程ID',
  `trip_name` varchar(100) NOT NULL COMMENT '行程简称',
  `trip_info` varchar(100) NOT NULL COMMENT '行程信息',
  `trip_add_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `trips`
--

INSERT INTO `trips` (`trip_id`, `trip_name`, `trip_info`, `trip_add_time`) VALUES
(1, '东线', '兵马俑、华清宫', '2019-10-30 14:17:45'),
(2, '西线', '乾陵、法门寺', '2019-10-30 14:18:07'),
(3, '华山', '华山', '2019-10-30 14:18:17'),
(4, '北二', '黄帝陵、壶口瀑布', '2019-10-30 14:18:47'),
(5, '北二', '王家坪或杨家岭、枣园革命旧址', '2019-10-31 17:41:24'),
(6, '北一', '壶口瀑布、黄帝陵', '2019-10-31 17:41:56'),
(7, '市内', '明城墙、大雁塔广场、钟鼓楼广场', '2019-10-31 17:42:23'),
(8, '自由活动', '无', '2019-12-02 10:55:05');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` int(100) UNSIGNED NOT NULL,
  `userName` varchar(100) NOT NULL COMMENT '代表人姓名',
  `iphone` char(30) NOT NULL COMMENT '联系方式',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `aid` tinyint(10) NOT NULL COMMENT '提交者ID',
  `order_number` int(10) NOT NULL COMMENT '订单号1000-999999',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态：0上架，1草稿，2下架',
  `trip_start_time` char(15) NOT NULL COMMENT '出团日期',
  `cid` int(1) NOT NULL COMMENT '所属公司'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `userName`, `iphone`, `add_time`, `aid`, `order_number`, `status`, `trip_start_time`, `cid`) VALUES
(8, '王文博', '18091832420', '2019-12-02 12:33:40', 1, 365728, 0, '2019-12-02', 1),
(9, '李玢', '15291838402', '2019-12-02 16:51:14', 1, 410381, 0, '2019-12-03', 1),
(10, '李娟', '15291838406', '2019-12-19 19:30:04', 1, 402444, 0, '2019-12-21', 1);

-- --------------------------------------------------------

--
-- 表的结构 `user_list`
--

CREATE TABLE `user_list` (
  `id` int(100) NOT NULL,
  `uid` int(10) NOT NULL COMMENT '父级ID',
  `user_name` varchar(100) NOT NULL COMMENT '游客姓名',
  `user_id` varchar(100) NOT NULL COMMENT '游客身份证'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_list`
--

INSERT INTO `user_list` (`id`, `uid`, `user_name`, `user_id`) VALUES
(7, 8, '[\"\\u738b\\u6587\\u535a\"]', '[\"610524199307167618\"]'),
(8, 9, '[\"\\u674e\\u73a2\"]', '[\"610111198712160024\"]'),
(9, 10, '[\"\\u674e\\u5a1f\",\"\\u674e\\u5b5f\\u7199\"]', '[\"610524199307167619\",\"610524200808187611\"]');

-- --------------------------------------------------------

--
-- 表的结构 `user_traffic`
--

CREATE TABLE `user_traffic` (
  `id` int(30) UNSIGNED NOT NULL COMMENT 'ID',
  `traffic` varchar(20) NOT NULL COMMENT '交通方式',
  `add_time` datetime NOT NULL COMMENT '添加日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_traffic`
--

INSERT INTO `user_traffic` (`id`, `traffic`, `add_time`) VALUES
(1, '自行抵达', '2019-11-17 15:25:32'),
(2, '自驾', '2019-11-17 15:25:48'),
(3, '汽车', '2019-11-17 15:26:14'),
(4, '火车', '2019-11-17 15:26:24'),
(5, '高铁', '2019-11-17 15:26:33'),
(6, '飞机', '2019-11-17 15:26:43');

-- --------------------------------------------------------

--
-- 表的结构 `user_trip`
--

CREATE TABLE `user_trip` (
  `id` int(100) UNSIGNED NOT NULL COMMENT '行程ID',
  `uid` int(100) NOT NULL COMMENT '用户ID',
  `arrive_traffic` int(1) NOT NULL COMMENT '抵达方式',
  `arrive_time` char(20) NOT NULL COMMENT '到达时间',
  `arrive_info` varchar(30) NOT NULL COMMENT '抵达信息',
  `return_traffic` int(1) NOT NULL COMMENT '返回方式',
  `return_time` char(20) DEFAULT NULL COMMENT '返程时间',
  `return_info` varchar(30) DEFAULT NULL COMMENT '返回信息',
  `meal` int(1) NOT NULL COMMENT '含餐方式：0不含餐，1含餐 ',
  `hotel` int(1) NOT NULL COMMENT '酒店标准',
  `hotel_type` text COMMENT '住宿类型',
  `hotel_time` text COMMENT '住宿时间',
  `user_info` text NOT NULL COMMENT '用户费用信息',
  `remarks` varchar(255) DEFAULT NULL COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_trip`
--

INSERT INTO `user_trip` (`id`, `uid`, `arrive_traffic`, `arrive_time`, `arrive_info`, `return_traffic`, `return_time`, `return_info`, `meal`, `hotel`, `hotel_type`, `hotel_time`, `user_info`, `remarks`) VALUES
(7, 8, 2, '2019-12-02 22:03:03', '第一时间收钱', 2, '2019-12-05 16:00:00', '0', 1, 1, '{\"big_house\":1}', '2019-12-2,2019-12-3,2019-12-4,2019-12-5', '{\"adult\":1,\"student\":0,\"children\":0}', ''),
(8, 9, 6, '2019-12-02 20:00:00', 'MU5510', 6, '2019-12-08 20:00:00', 'MU5152', 1, 1, '{\"big_house\":1}', '2019-12-2,2019-12-3,2019-12-4,2019-12-5,2019-12-6,2019-12-7', '{\"adult\":1,\"student\":0,\"children\":0}', ''),
(9, 10, 6, '2019-12-20 08:05:04', 'MU5622', 6, '2019-12-23 22:04:04', 'MU5677', 1, 1, '{\"big_house\":1}', '2019-12-20,2019-12-21,2019-12-22', '{\"adult\":1,\"student\":0,\"children\":1}', '');

-- --------------------------------------------------------

--
-- 表的结构 `user_trip_list`
--

CREATE TABLE `user_trip_list` (
  `id` int(100) NOT NULL COMMENT '行程ID',
  `uid` int(100) NOT NULL COMMENT '用户ID',
  `trip_time` char(20) NOT NULL COMMENT '出行时间',
  `trip` int(10) NOT NULL COMMENT '行程内容',
  `status` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT '0' COMMENT '状态：0正常，1已出团，2未出图，3行程中'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_trip_list`
--

INSERT INTO `user_trip_list` (`id`, `uid`, `trip_time`, `trip`, `status`) VALUES
(5, 8, '2019-12-02', 1, 0),
(6, 8, '2019-12-03', 2, 0),
(7, 8, '2019-12-04', 3, 0),
(8, 9, '2019-12-03', 1, 0),
(9, 9, '2019-12-04', 3, 0),
(10, 9, '2019-12-05', 2, 0),
(11, 9, '2019-12-06', 4, 0),
(12, 9, '2019-12-07', 5, 0),
(13, 9, '2019-12-08', 7, 0),
(14, 10, '2019-12-21', 1, 0),
(15, 10, '2019-12-22', 3, 0),
(16, 10, '2019-12-23', 2, 0);

--
-- 转储表的索引
--

--
-- 表的索引 `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `admin_company`
--
ALTER TABLE `admin_company`
  ADD PRIMARY KEY (`cid`);

--
-- 表的索引 `admin_groups`
--
ALTER TABLE `admin_groups`
  ADD PRIMARY KEY (`gid`);

--
-- 表的索引 `admin_menus`
--
ALTER TABLE `admin_menus`
  ADD PRIMARY KEY (`mid`);

--
-- 表的索引 `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_list`
--
ALTER TABLE `user_list`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_traffic`
--
ALTER TABLE `user_traffic`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_trip`
--
ALTER TABLE `user_trip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_trip_list`
--
ALTER TABLE `user_trip_list`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `admin_company`
--
ALTER TABLE `admin_company`
  MODIFY `cid` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `admin_groups`
--
ALTER TABLE `admin_groups`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色ID', AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `admin_menus`
--
ALTER TABLE `admin_menus`
  MODIFY `mid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=20;

--
-- 使用表AUTO_INCREMENT `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(20) NOT NULL AUTO_INCREMENT COMMENT '行程ID', AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `user_list`
--
ALTER TABLE `user_list`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `user_traffic`
--
ALTER TABLE `user_traffic`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `user_trip`
--
ALTER TABLE `user_trip`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '行程ID', AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `user_trip_list`
--
ALTER TABLE `user_trip_list`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT COMMENT '行程ID', AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
