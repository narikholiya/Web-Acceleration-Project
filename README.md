# 🌐 Multi-Region Web Application Acceleration with High Availability using AWS Global Accelerator

## 📘 Project Summary
This project demonstrates a highly available, fault-tolerant, and globally distributed web application infrastructure using **AWS Global Accelerator**. The solution is deployed across two AWS regions — **Asia Pacific (Mumbai)** and **US East (N. Virginia)** — ensuring low-latency access, disaster recovery, and regional failover capability.

## 🛠️ Key Components
** for intelligent routing and global traffic distribution.
- **Application Load Balancers (ALB)** in both regions to manage web traffic.
- **Elastic File System (EFS)** for shared file storage across EC2 instances in each region.
- **Auto Scaling Groups** for scaling web servers based on demand.
- **Public & Private Subnet Architecture** for secure and efficient network segmentation.
- **Bastion Hosts** and **NAT Gateways** for secure access and outbound internet traffic.
- **Separate Frontend (Web Servers)** and **Backend (Database/Server-side Logic)** layers deployed in private subnets.

## 🌍 Architecture Benefits
- 🌎 **Global High Availability** – Automatic failover between Mumbai and N. Virginia.
- ⚡ **Low Latency** – Users are routed to the nearest healthy region.
- 🔒 **Secure Infrastructure** – Bastion hosts and NAT gateways enhance network security.
- 📈 **Scalability** – Auto scaling ensures consistent performance under load.
- 📁 **Shared File System** – EFS enables consistency across frontend servers in a region.

## 🖼️ Architecture Diagram
- **AWS Global Accelerator![Web Global Acceleration](https://github.com/user-attachments/assets/76c9fa8a-7d2a-4d10-95d3-1df7c467af34)
