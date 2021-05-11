# -*- mode: ruby -*-
# vi: set ft=ruby :

required_plugins = %w( vagrant-hostsupdater )
required_plugins.each do |plugin|
  system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
end

Vagrant.configure("2") do |config|
  # Available Boxes: https://atlas.hashicorp.com/search
  config.vm.box = "ubuntu/xenial64"

  # Virtual Machine will be available at 10.10.10.200:80
  config.vm.network "private_network", ip: "10.10.10.200"
  # TODO https://switchcaseblog.wordpress.com/2016/02/22/creating-a-self-signed-ssl-for-local-development-with-vagrant-nginx/
  config.vm.network "private_network", ip: "10.10.10.200", guest: 443, host: 443
  config.hostsupdater.aliases = ["devstarter.local"]
  config.hostsupdater.remove_on_suspend = true

  host = RbConfig::CONFIG['host_os']

  # Synced folder
  config.vm.synced_folder "./", "/vagrant", disabled: true
  if host =~ /darwin|linux/

    # Using NFS for nix-systems
    config.vm.synced_folder "./", "/srv/devstarter.local",
        create: true,
        nfs: true,
        # This fixes update lag https://github.com/hashicorp/vagrant/issues/9267
        :mount_options => ['actimeo=1']

  elsif host =~ /mswin|mingw|cygwin/

    # Standard "shared folders" fallback for windows
    config.vm.synced_folder "./", "/srv/devstarter.local", create: true, mount_options: ['dmode=774','fmode=775']

  end

  # VirtualBox settings
  config.vm.provider "virtualbox" do |v|
    # Don't boot with headless mode
    v.gui = false

    # Use VBoxManage to customize the VM
    v.customize ["modifyvm", :id, "--cpuexecutioncap",      "95"]
    v.customize ["modifyvm", :id, "--natdnshostresolver1",  "on"]
    v.customize ["modifyvm", :id, "--natdnsproxy1",         "on"]

    # Give VM 1/4 system memory
    if host =~ /darwin/
        # sysctl returns Bytes and we need to convert to MB
        mem = `sysctl -n hw.memsize`.to_i / 1024
    elsif host =~ /linux/
        # meminfo shows KB and we need to convert to MB
        mem = `grep 'MemTotal' /proc/meminfo | sed -e 's/MemTotal://' -e 's/ kB//'`.to_i
    elsif host =~ /mswin|mingw|cygwin/
        # Windows code via https://github.com/rdsubhas/vagrant-faster
        mem = `wmic computersystem Get TotalPhysicalMemory`.split[1].to_i / 1024
    end

    mem = mem / 1024 / 4
    v.customize ["modifyvm", :id, "--memory", mem]

  end

  # TODO Use own vagrant package https://stefanwrobel.com/how-to-make-vagrant-performance-not-suck

  # Installing the required packages and internal workflow
  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
  config.vm.provision "shell", path: ".provision/run_once.sh"
  config.vm.provision "shell", run: "always", path: ".provision/run_always.sh"

end
