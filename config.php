<?php

/* ��������� ������� */

//��������� ��������
$config['page_charset'] = 'utf-8';


/*��������� �����*/

//�����������
$config['path']['controller'] = '/controller/';
//����
$config['path']['view'] = '/view/';
//�������
$config['path']['section'] = '/sections/';
//��������� �����
$config['path']['temp'] = '/tmp/';
//�������
$config['path']['avatar'] = '/avatars/';
//���������
$config['path']['include'] = '/include/';


/* ��������� ������ */

//������� (� ��������). ������ ����������, � ������� �������� ������ ������ ��������� ������������� ������ ����������� �� ��������
$config['pulse']['frequency'] = 20;
//������������ ���������� (� ��������). ���������� �������� ����� ��������� ������������� � ��� ���������� �� ������� 
$config['pulse']['max_diff'] = 10;


/* ��������� �������� */

//���������� ���������� �� ��������
$config['section']['page_size'] = 10;
//���������� ������� ������ �� �������� � ������������
$config['section']['pages_per_page'] = 3;


/* ��������� ���� ������ */

//����
$config['db']['host'] = 'localhost';
//��� ������������
$config['db']['user'] = 'root';
//������
$config['db']['pass'] = '71295';
//��� ����
$config['db']['db'] = 'planetsbook';
//��������� ��������
$config['db']['charset'] = 'utf8';
//��������� ��������� ������� ��� �������. TRUE - ����������� ����������, FALSE - ���������� FALSE
$config['db']['throwable'] = TRUE;
