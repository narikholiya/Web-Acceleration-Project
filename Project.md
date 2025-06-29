# 🌐 Multi-Region Web Acceleration with High Availability using AWS Global Accelerator

## 🎯 Objective

This project demonstrates a highly available, fault-tolerant, and globally low-latency web application deployment by utilizing resources across two AWS regions:  
- **Asia Pacific (Mumbai)** - `ap-south-1`  
- **US East (N. Virginia)** - `us-east-1`

The **AWS Global Accelerator** ensures that global users are automatically routed to the nearest healthy region, optimizing both performance and uptime.

## 🧩 Key Components

- **AWS Global Accelerator**  
  Directs user traffic to the optimal regional endpoint based on latency and health status, ensuring low-latency access and high availability.

- **Amazon VPC (India & USA)**  
  Two isolated Virtual Private Clouds, each configured with public and private subnets to host application components securely and efficiently.

- **Elastic Load Balancer (ALB)**  
  Distributes incoming traffic across EC2 instances in Auto Scaling groups located in private subnets, improving scalability and fault tolerance.

- **Amazon EC2 Instances (Web Servers)**  
  EC2 instances are launched in private subnets where Apache web servers host the frontend application. These servers are part of Auto Scaling groups for high availability.

- **Amazon EC2 Auto Scaling**  
  Automatically launches or terminates web server EC2 instances based on demand to maintain performance and cost-efficiency.

- **Amazon RDS (MySQL)**  
  A managed relational database deployed in private subnets, accessible only from web server instances, ensuring secure and reliable data storage.

- **Amazon EFS**  
  A shared file storage system mounted on web servers in each region, allowing consistent frontend content across all instances.

- **NAT Gateway & Internet Gateway**
  - **Internet Gateway (IGW):** Provides internet access to public subnets.  
  - **NAT Gateway:** Allows instances in private subnets to access the internet for updates without being exposed directly.

- **Bastion Host**  
  Deployed in public subnets to securely connect (via SSH) to private subnet resources, such as EC2 web and database servers.

## 📘 Project Overview

### 🔧 Creating a Virtual Private Cloud (VPC)

#### 1. VPC Setup (Region: `ap-south-1` - India)

- Create a custom VPC with a CIDR block `192.168.100.0/24` to provide an isolated network environment.
- Create public and private subnets across multiple Availability Zones to support high availability.
- Attach an **Internet Gateway (IGW)** to the VPC and update the route table to enable internet access for public subnets.
- Create a **NAT Gateway** in the public subnet to allow outbound internet access from the private subnet (for updates and web server installation).
- Configure route tables for both public and private subnets with appropriate routing rules.

### 🛣️ Route Table Configuration

- Create a route table for the **public subnet** and associate it with the public subnets.
  - Add a route: `0.0.0.0/0 → Internet Gateway (IGW)` to enable internet access.

- Create a route table for the **private subnet** and associate it with the private subnets.
  - Add a route: `0.0.0.0/0 → NAT Gateway` to enable outbound internet access while keeping instances private.

---

### 🌐 VPC Configuration (India Region - `ap-south-1`)

#### 📏 CIDR Blocks
- VPC: `192.168.100.0/24`

#### 🧱 Subnets
- **Public Subnet-A**: `192.168.100.0/26`  
- **Public Subnet-B**: `192.168.100.64/26`  
- **Private Subnet-A**: `192.168.100.128/26`  
- **Private Subnet-B**: `192.168.100.192/26`  

#### 🏗️ Components in Public Subnets
- **Bastion Host** (for secure SSH access to private instances)  
- **NAT Gateway**

#### 🔐 Components in Private Subnets
- **Auto Scaling EC2 Web Servers**  
- **Amazon EFS Mount Targets**  
- **Amazon RDS Instance (MySQL)**

### 🔧 2. VPC Setup (Region: `us-east-1` - USA)

- Create a custom VPC with a CIDR block `192.168.200.0/24` to provide an isolated network environment.
- Create public and private subnets across multiple Availability Zones to support high availability.
- Attach an **Internet Gateway (IGW)** to the VPC and update the route table to enable internet access for public subnets.
- Create a **NAT Gateway** in the public subnet to allow outbound internet access from the private subnet (used for updates and installing web servers).
- Configure route tables for both public and private subnets with appropriate routing rules.

### 🛣️ Route Table Configuration

- Create a route table for the **public subnet** and associate it with the public subnets.
  - Add a route: `0.0.0.0/0 → Internet Gateway (IGW)` to enable internet access.

- Create a route table for the **private subnet** and associate it with the private subnets.
  - Add a route: `0.0.0.0/0 → NAT Gateway` to enable outbound internet access while keeping instances private.

---

### 🌐 VPC Configuration (USA Region - `us-east-1`)

#### 📏 CIDR Blocks
- VPC: `192.168.200.0/24`

#### 🧱 Subnets

- **Public Subnet-A**: `192.168.200.0/26`  
- **Public Subnet-B**: `192.168.200.64/26`  
- **Private Subnet-A**: `192.168.200.128/26`  
- **Private Subnet-B**: `192.168.200.192/26`  

#### 🏗️ Components in Public Subnets

- **Bastion Host** (SSH Access)
- **NAT Gateway**

#### 🔐 Components in Private Subnets

- **Auto Scaling EC2 Web Servers**
- **Amazon EFS Mount Targets**
- **Amazon RDS Instance (MySQL)**

---

### 🔐 Security Group Configuration

- **NACLs (Network Access Control Lists):**  
  Optional additional layer of network security used to control traffic at the subnet level.

- **IAM Roles:**  
  EC2 instances are assigned IAM roles to securely access:
  - **Amazon EFS**  
  - **Amazon CloudWatch** for logging and monitoring

### 🔐 Web Server Security Groups

- **Web-Server-SG-IN**: Attached to web servers in **India**
- **Web-Server-SG-US**: Attached to web servers in **USA**

#### ✅ Inbound Rules:
- Allow **HTTP (port 80)** and **HTTPS (port 443)** from the **ALB Security Group**
- Allow **MySQL (port 3306)** from the **RDS Security Group** for application-database interaction

#### 📤 Outbound Rules:
- Allow **all traffic** to enable updates and communication (can be restricted for production)
- Ensure **port 2049 (NFS)** is open for **Amazon EFS** access

---

### 📁 EFS Security Group (`EFS-SG`)

#### ✅ Inbound Rules (Allow Mount Access):

| Type | Protocol | Port Range | Source             | Description                        |
|------|----------|------------|--------------------|------------------------------------|
| NFS  | TCP      | 2049       | Web-Server-SG-IN   | Allow web servers from India region |
| NFS  | TCP      | 2049       | Web-Server-SG-US   | Allow web servers from USA region  |

#### 📤 Outbound Rules:

| Type       | Protocol | Port Range | Destination | Description                |
|------------|----------|------------|-------------|----------------------------|
| All Traffic| All      | All        | `0.0.0.0/0` | (Default) Allow all outbound traffic |

### 🛡️ RDS Security Group (`RDS-SG`)

#### ✅ Inbound Rules:
- Allow **MySQL/Aurora (port 3306)** only from the **Web Server Security Group**

#### 📤 Outbound Rules:
- Allow **all traffic** (default setting unless using VPC Peering or AWS PrivateLink)

---

### 🧳 Bastion Host Security Group (`Bastion-SG`)

#### ✅ Inbound Rules:
- Allow **SSH (port 22)** only from your **specific IP address** (e.g., home or office static IP)

#### 📤 Outbound Rules:
- Allow **all traffic** (used for administrative tasks like system updates and Git repository access)

---

### 🌐 Application Load Balancer Security Group (`ALB-SG`)

#### ✅ Inbound Rules:
- Allow **HTTP (port 80)** and **HTTPS (port 443)** from **anywhere** (`0.0.0.0/0`)  
  *(You may restrict this by region or IP as per security policies)*

#### 📤 Outbound Rules:
- Allow traffic to the **Web Server Security Group** on **ports 80 and 443**


## 🚀 Deployment Configuration

### 🖥️ Web Server Configuration (Frontend Deployment)

- **Tech Stack:** HTML, CSS, PHP (any framework can be used)  
- **Deployment Details:**  
  - Frontend files are hosted on EC2 instances (web servers) located in **private subnets**.  
  - **Amazon EFS** is used to keep frontend files synchronized across multiple web servers.  
  - **Apache** web server is configured to serve the frontend application.

---

### 🗄️ RDS Database Setup

- **Engine:** MySQL  
- **Deployment:** Located in **Private Subnet-A** and **Private Subnet-B**  
- **Security:**  
  - Accessible **only** from the Web Server Security Group.  
  - Port **3306** is open **only** to internal traffic.  
- **High Availability:** Enabled with Multi Availability Zone (Multi-AZ) deployment for fault tolerance.

## ⚙️ EC2, AMI, and Auto Scaling Setup for Web Server with EFS + RDS Integration

### Step 1: Launch EC2 Instance (Free Tier)
- Launch a **t2.micro** EC2 instance in the **public subnet** (for initial configuration).  
- Choose **Amazon Linux 2** or **Ubuntu** as the base AMI.  
- Use a **key pair** for SSH access.  
- Assign a security group allowing **SSH (port 22)** and **HTTP (port 80)** if needed.  
- Attach an **IAM role** with permissions to access **EFS** and **SSM** (optional).

### Step 2: Install and Configure Web Server
- SSH into the EC2 instance
- Install and start Apache
  # Update package repositories
sudo yum update -y

# Install Apache (httpd)
sudo yum install -y httpd

# Start the Apache service
sudo systemctl start httpd

# Enable Apache to start on boot
sudo systemctl enable httpd

### Step 3: Mount EFS
- sudo yum install -y amazon-efs-utils
Mount EFS to a directory (e.g., /var/www/html):
- sudo mkdir -p /var/www/html
- sudo mount -t efs fs-xxxxxxx:/ /var/www/html
Add EFS to /etc/fstab for auto-mount on boot.

### Step 4: Add Web Files and Configurations

- Deploy website content in `/var/www/html` (EFS mount).
- Web server now serves content stored in EFS — shared across all future EC2s.
  
### Step 5: Install MySQL Client & Store Credentials

**Install MySQL client:**
- sudo yum install -y mysql
Create a credentials file in EFS (/var/www/html/db-config.php):
<?php
$dbhost = "your-rds-endpoint";
$dbuser = "admin";
$dbpass = "your-password";
$dbname = "your-db-name";
?>

### Step 6: Test RDS Connection

Use the following command to test the connection to your RDS MySQL database:
-mysql -h your-rds-endpoint -u admin -p

### Step 7: Create AMI from Configured EC2

- Stop the instance (optional).
- Go to **EC2 Console** → **Actions** > **Create Image (AMI)**.
- Name it something like: `webserver-efs-rds-base-ami`.

---

### Step 8: Create Launch Template

- Go to **EC2** → **Launch Templates** → **Create Template**.
- Select your **AMI ID**.
- Choose the following configuration:
  - **Instance type**: `t2-micro` (for free tier / testing)
  - **Key pair**
  - **IAM role**
  - **Security group**: Web Server SG
  - **User data** (if needed for remounting EFS or restarting services)

- Click **Save** to create the launch template.

# AWS Infrastructure Components Overview

## Auto Scaling Group
- Uses the launch template created earlier.
- Automatically adjusts the number of EC2 instances based on traffic patterns.
- Maintains performance and reduces costs by adding or removing instances dynamically.
- Integrated with Application Load Balancer (ALB) health checks to replace unhealthy instances.

## Elastic File System (EFS)
- Mounted on all web servers across Availability Zones within each region.
- Provides a shared file system for storing:
  - Static assets (images, HTML, CSS)
  - User uploads
  - Web content that needs to be consistent across instances
- Ensures data consistency and centralized storage.

## Application Load Balancer (ALB)
- Deployed in each region to distribute traffic evenly across web servers.
- Integrated with Auto Scaling Groups.
- Registered targets are EC2 instances located in private subnets.
- Accepts requests from AWS Global Accelerator and routes them to the appropriate backend instances.

## Global Accelerator Configuration
**Purpose:**  
Provides a single static IP address for your global application and routes traffic to the optimal AWS region based on latency and health checks.

## Integration
- Routes traffic to Application Load Balancers (ALBs) in both India and USA regions.
- Ensures users worldwide reach the nearest, most responsive infrastructure.

- **Listener:** TCP on ports 80 and 443  
- **Endpoints:** ALBs in `ap-south-1` and `us-east-1`  
- **Health checks:** Routes traffic only to healthy endpoints  
- **Traffic Distribution:** Automatically routes users to the closest healthy region  

## Benefits
- Reduced latency for users across the globe.  
- High availability through multi-region failover.  
- Scalable frontend leveraging Auto Scaling.  
- Centralized and persistent storage with Elastic File System (EFS).  
- Managed database layer using Amazon RDS.  
- Simplified SSH management via Bastion Host.  

## Testing & Validation
- Test access from different global locations using a VPN or tools like GeoPeeker (to see how the site appears worldwide).  
- Simulate instance failure and validate Global Accelerator’s failover capabilities.  
- Verify EFS synchronization across web servers.  
- Confirm database connectivity from the web layer.






