from fabric.api import *

@task
def login():
	local('mysql --user=' + env.DB_USER + ' --password=' + env.DB_PASSWORD + ' ' + env.DB_NAME)
	
@task
def dump(dest='db.sql'):
	local('mysqldump --user=' + env.DB_USER + ' --password=' + env.DB_PASSWORD + ' ' + env.DB_NAME + ' > ' + dest)
	
@task
def imp(src='db.sql'):
	local('mysql --user=' + env.DB_USER + ' --password=' + env.DB_PASSWORD + ' ' + env.DB_NAME + ' < ' + src)

@task
def pull():
	with cd(env.DEPLOY_PATH + '/app'):
		dest = 'db_' + env.DEPLOY_USER + '.sql'
		dest_arc = dest + '.tar.gz'
		run('fab db.dump:' + dest)
		run('tar -czf ' + dest_arc + ' ' + dest)
		get(dest_arc, dest_arc)
		local('tar -xf ' + dest_arc)
		imp(dest)

@task
def sql_user():
	sql = "GRANT ALL PRIVILEGES ON " + env.DB_NAME + ".* TO " + env.DB_USER + "@localhost IDENTIFIED BY '" + env.DB_PASSWORD + "' WITH GRANT OPTION;"
	print sql
	#local('echo ' + sql + ' | mysql --user=' + env.DB_USER + ' --password=' + env.DB_PASSWORD + ' ' + env.DB_NAME)