<?php
namespace Squanch\Enum;


class Callbacks
{
	use \Objection\TEnum;
	
	
	const SUCCESS_ON_GET 	= 'successOnGet';
	const MISS_ON_GET    	= 'missOnGet';
	const ON_GET         	= 'onGet';

	const SUCCESS_ON_HAS    = 'successOnHas';
	const FAIL_ON_HAS		= 'failOnHas';
	const ON_HAS			= 'onHas';
	
	const SUCCESS_ON_SET    = 'successOnSet';
	const FAIL_ON_SET       = 'failOnSet';
	const ON_SET			= 'onSet';
	
	const SUCCESS_ON_DELETE = 'successOnDelete';
	const FAIL_ON_DELETE    = 'failOnDelete';
	const ON_DELETE			= 'onDelete';
}