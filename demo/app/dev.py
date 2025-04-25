from fabric.api import *
import sys, os, os.path, datetime, shutil, platform, subprocess, re
import db

# for windows add path to GIT
git_path = "C:\\Program Files (x86)\\Git\\bin"
if platform.system() == 'Windows':
	os.environ['PATH'] = os.environ['PATH'] + ';' + git_path + ';'

env.hosts = [env.DEPLOY_USER + '@' + env.DEPLOY_SERVER + ':' + env.DEPLOY_PORT]

def make_changes_file(dest):
	if os.path.exists(dest):
		os.unlink(dest)
	with open(dest, 'w') as fout:
		cmd = subprocess.Popen('git status', shell=True, stdout=subprocess.PIPE)
		for line in cmd.stdout:
			if 'modified:' in line:
				fout.write(re.split('\s+', line)[2] + '\n')
			if 'new file:' in line:
				fout.write(re.split('\s+', line)[3] + '\n')

@task
def deploy():
	fname_changes = 'app/tmp/changes-filelist.txt'
	tmp_dir = 'tmp/changes'
	
	if os.path.exists(tmp_dir):
		shutil.rmtree(tmp_dir)
	os.mkdir(tmp_dir)
	os.chdir('..')
	
	local('git add .')
	local('git status')
	make_changes_file(fname_changes)	
	with open(fname_changes) as f:
		for line in f:
			line = line.strip()
			if env.DEPLOY_METHOD == 'DEFAULT':
				put(line, env.DEPLOY_PATH + '/' + line)
			else:
				try:
					os.makedirs(os.path.dirname('app/tmp/changes/' + line))
				except:
					pass
				try:
					shutil.copyfile(line, 'app/tmp/changes/' + line)				
				except:
					pass
	
	if env.DEPLOY_METHOD == 'FTP':
		os.chdir('app/tmp/changes')		
		local('lftp -e "mirror -R -v -p --parallel=3 ./ ' + env.DEPLOY_PATH + ' && exit" '
			+ env.DEPLOY_USER + ':' + env.DEPLOY_PASSWORD + '@' + env.DEPLOY_SERVER)
	#elif env.DEPLOY_METHOD == 'SCP':
	#	print 'by SCP'
		#cd ./_backup/tmpchanges
		#scp -P $DEPLOY_PORT -r ./ "$DEPLOY_USER@$DEPLOY_SERVER:$DEPLOY_PATH/"	
	#elif env.DEPLOY_METHOD == 'PUTTY':	
	#	print 'by PUTTY'
		#pscp -i "$PUTTY_KEY_PATH" -agent -P $DEPLOY_PORT -r ./_backup/tmpchanges/ "$DEPLOY_USER@$DEPLOY_SERVER:$DEPLOY_PATH/"
	#elif env.DEPLOY_METHOD == 'CURL':	
	#	print 'by CURL'
		#cd ./_backup/tmpchanges	
		#for i in $(cat ../../changes-filelist.txt)
		#do
		#	curl --upload-file $i -u $DEPLOY_USER:$DEPLOY_PASSWORD ftp://$DEPLOY_SERVER/$DEPLOY_PATH/$i
		#done

@task
def backup():
	dest = datetime.datetime.now().strftime('%Y-%m-%d')
	db.dump('db_' + dest + '.sql')	
	exclude_param = ''
	if os.path.exists('arc-exclude-list'):
		exclude_param = ' --exclude-from=app/arc-exclude-list '

	dest = 'backup_' + dest	+ '.tar'
	os.chdir('..')
	local('tar -cf ' + dest + ' ./ --exclude=' + dest + exclude_param)

@task
def chmod():
	with cd(env.DEPLOY_PATH + '/app'):
		run('./site-chmod')

@task
def push():
	chmod()
	local('git push origin master')

@task
def deploy_production():
	print 'deploy to production...'
	with cd(env.DEPLOY_PATH + '/app'):	
		run('fab deploy')
