TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`%` TRIGGER `test` BEFORE INSERT ON `user` FOR EACH ROW begin\n\nset NEW.password = MD5(NEW.password);\n\nEND'
sql_modes=1436549152
definers='root@%'
client_cs_names='utf8'
connection_cl_names='utf8_general_ci'
db_cl_names='utf8_general_ci'
created=152387384192
