from fabric.api import *
import os, getpass

user_name = getpass.getuser()

@task
def list():
	print 'Hosts list:'
	print '-----------'
	for f in os.listdir('/etc/apache2/sites-available'):
		if f != 'default' and f != 'default-ssl':
			print 'http://' + f
	print ''

@task
def enable(src):
	hostname = user_name + '.' + src
	local('sudo a2ensite ' + hostname)

	tmp = hostname + '.tmp.hosts'
	local('cat /etc/hosts > ' + tmp)
	local('echo "127.0.0.1	' + hostname + '" >> ' + tmp)
	local('sudo mv ' + tmp + ' /etc/hosts')
	
	local('sudo service apache2 reload')

@task
def disable(src):
	hostname = user_name + '.' + src
	local('sudo a2dissite ' + hostname)

	tmp = hostname + '.tmp.hosts'
	local('cat /etc/hosts | awk "\$2 !~ /' + hostname + '/ { print; }" > ' + tmp)
	local('sudo mv ' + tmp + ' /etc/hosts')

	local('sudo service apache2 reload')

@task
def add(src, path=''):
	hostname = user_name + '.' + src

	conf_path = '/etc/apache2/sites-available/' + hostname
	site_path = os.getcwd() + '/' + src
	if path != '':
		site_path += '/' + src

	tmp = hostname + '.tmp.conf'
	with open('/etc/apache2/sites-available/default') as fin:
		content = fin.read().replace('/var/www', site_path).replace('default-site', hostname)		
		with open(tmp, 'w') as fout:
			fout.write(content)
			fout.close()
			local('sudo cp ' + tmp + ' ' + conf_path)
			os.unlink(tmp)
			enable(src)

@task
def remove(src):
	hostname = user_name + '.' + src

	disable(src)
	local('sudo rm /etc/apache2/sites-available/' + hostname)
