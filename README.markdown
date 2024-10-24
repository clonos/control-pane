# ClonOS Project

#### Table of Contents

1. [Project Description - What does the project do?](#project-description)
2. [Usage - Configuration options and additional functionality](#usage)
3. [Limitations - OS compatibility, etc.](#limitations)
4. [Contributing - Contribute the project](#contributing)

## project description

ClonOS is a free open-source FreeBSD-based platform for virtual environments creation and management. In the core:

- https://www.freebsd.org :: FreeBSD OS
  + https://man.freebsd.org/bhyve/8 :: bhyve(8) as hypervisor engine
  + https://xenproject.org/ :: Xen as hypervisor engine
  + https://man.freebsd.org/vale/4 :: vale(4) as Virtual Ethernet Switch
  + http://man.freebsd.org/jail/8 :: jail(8) as container engine

- https://www.bsdstore.ru/en/ :: CBSD Project as management tools

- https://puppet.com/ :: Puppet as configuration management

We like existing Linux-only solutions such as OpenStack (https://www.openstack.org/), OpenNebula (http://opennebula.org/), Amazon AWS (https://aws.amazon.com/) and we believe that FreeBSD OS is able to give something similar.

## Usage

For installing from ISO: Use downloads pages https://clonos.convectix.com/download.html to obtain latest .ISO image
For installing on FreeBSD: https://clonos.convectix.com/installation_on_freebsd.html

## Limitations

Tested with following OSes and distribution:

- FreeBSD 14x, 15x

Errata: https://clonos.convectix.com/errata.html

## Contributing

* Fork me on GitHub: [git@github.com:clonos/control-pane.git](git@github.com:clonos/control-pane.git)
* Commit your changes (`git commit -am 'Added some feature'`)
* Push to the branch (`git push`)
* Create new Pull Request
