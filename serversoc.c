#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <netdb.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <arpa/inet.h> 
#include <mysql/my_global.h>
#include <mysql/mysql.h>

static char *host = "localhost";
static char *user = "root";
static char *pswd = NULL;
static char *dbname = "sacco";

unsigned int port = 3306;
static char *unix_socket = NULL;
unsigned int flag = 0;

int main(int argc, char *argv[]){
	
	//Declare varibles and server address struct
	int sockfd = 0, bindno, accfd;
	struct sockaddr_in serv_addr;
	char buff[1024];
	char msg[1024];
	char key_one[30] = "contribution check";
	char key_two[30] = "benefits check";
	char key_three[30] = "loan status"; 
	char key_four[30] = "load repayment_details";
		//Variables and structures for API
		MYSQL *conn;
		MYSQL_RES *res;
		MYSQL_ROW row;
		char ps[32];
		int i;
		char mem_id[11];
		char qry[1024];

	//Create socket
	sockfd = socket(AF_INET, SOCK_STREAM, 0);
	if(sockfd < 0){
		perror("Error: Couldn't create socket!");
		exit(1);
	}
	
	//Assign values to address structure fields
	serv_addr.sin_family = AF_INET;
	serv_addr.sin_addr.s_addr = htonl(INADDR_ANY);
	serv_addr.sin_port = htons(5500);
	
	//Bind socket to address
	bindno = bind(sockfd, (struct sockaddr *)&serv_addr, sizeof(serv_addr));
	if(bindno < 0){
		perror("Error: Couldn't bind to address!");
		exit(1);
	} else {
		puts("Socket bound to address of port number 5500!");
	}
	
	//Listen for clients
	listen(sockfd, 4);
	puts("Listening...");
	
	//Accept incoming connections and receive messages
	wait: printf("Waiting for new clients.....");
	accfd = accept(sockfd, (struct sockaddr *) 0, 0);
	if(accfd == -1){
		perror("Couldn't accept connection!");
		exit(1);
	} 
		
	//Connect to MySQL database
	conn = mysql_init(NULL);
	if(!(mysql_real_connect(conn, host, user, pswd, dbname, port, unix_socket, flag))){
		fprintf(stderr, "\nError: %s [%d]\n", mysql_error(conn), mysql_errno(conn));
		exit(1);
	} else {
		puts("Connection successful!!!");	
	}
	
	//Receive username and password
	char userpswd[64];
	char user_name[32];
	char pass[32];
	char on_failure[64] = "Incorrect username or password\n\n";
	
	printf("USERNAME PASSWORD: ");
	recv(accfd, userpswd, sizeof(userpswd), 0);
	printf("%s", userpswd);
	
	//Split authentication string
	strcpy(user_name,strtok(userpswd, " "));
	strcpy(pass,strtok(NULL, " "));
	
	//Print the strings
	printf("user_name: %s \npass: %s", user_name, pass);
	
	//Fetch password for corresponding username
	sprintf(qry, "SELECT password FROM members WHERE members.username='%s';", user_name);
	mysql_query(conn, qry);
	res = mysql_store_result(conn);
	while((row = mysql_fetch_row(res))){
		printf("Password: %s\n", row[0]); //Print fetched password
		strcpy(ps, row[0]);
	}
	
	//Authentication by comparison
	i = strncmp(ps,pass, 9);
	printf("i: %d\n", i);
	
	if(i == 0){
		char success[32] = "Access granted...\n";
		send(accfd, success, sizeof(success),0);
	} else {
		send(accfd, on_failure, sizeof(on_failure),0);
	}	
	
	char log_out[8] = "logout";
	
	//Fetch id
	char id_qry[1024];
	char std_qry[1024];
	memset(mem_id, 0,sizeof(mem_id));
	
	sprintf(id_qry, "SELECT member_id FROM members WHERE username='%s';", user_name);
	mysql_query(conn, id_qry);
	res = mysql_store_result(conn);
	while((row = mysql_fetch_row(res))){
		printf("\nmember ID: %s\n", row[0]); //Print fetched id
		strcpy(mem_id, row[0]);
	}
	
	send(accfd, mem_id, sizeof(mem_id), 0);		// send member_id to client  
	
	while(1){
			//Initialize character arrays
			//memset(buff, 0, sizeof(buff));
			//memset(msg, 0, sizeof(msg));
			
			//Receive values
			int rcv_arg = recv(accfd, buff, sizeof(buff), 0);
			
			if(rcv_arg < 0){
				perror("Reading stream message error!");
			} else if(rcv_arg == 0){
				printf("Ending connection...\n");
			} else {
				printf("MSG: %s\n", buff);
			}
			printf("Message received, rcv_val = %d\n", rcv_arg);
			
			if(strncmp(log_out, buff, 6) == 0){
				break;
			}
			
			//API
			if(strncmp(buff, key_one, 18) == 0){	//Check contributions
				sprintf(std_qry, "SELECT sum(amount) FROM contributions WHERE member_id='%s';", mem_id);
				mysql_query(conn, std_qry);
				res = mysql_store_result(conn);
				while((row = mysql_fetch_row(res))){
					printf("\nTotal contribution: %s\n", row[0]); //Print fetched contribution total
					strcpy(msg, row[0]);
					strcat(msg, " is your total contribution.");
					printf("\n%s", msg);
					send(accfd, msg, sizeof(msg), 0);
				}
			} else if(strncmp(buff, key_two, 14) == 0){	//Check benefits
				sprintf(std_qry, "SELECT sum(benefits_amount) FROM benefits WHERE member_id='%s';", mem_id);
				mysql_query(conn, std_qry);
				res = mysql_store_result(conn);
				while((row = mysql_fetch_row(res))){
					printf("Total benefits: %s\n", row[0]); //Print fetched benefits total
					strcpy(msg, row[0]);
					strcat(msg, " is your total benefit.");
					send(accfd, msg, sizeof(msg), 0);
				}
			} else if(strncmp(buff, key_three, 11) == 0){	//Check loan status
				sprintf(std_qry, "SELECT loan_status FROM loan WHERE member_id='%s';", mem_id);
				mysql_query(conn, std_qry);
				res = mysql_store_result(conn);
				while((row = mysql_fetch_row(res))){
					printf("Loan status: %s\n", row[0]); //Print fetched loan status
					strcpy(msg, row[0]);
					send(accfd, msg, sizeof(msg), 0);
				}
			} else if(strncmp(buff, key_four, 22) == 0){	//Check loan repayment_details
				sprintf(std_qry, "SELECT repayment_amount FROM repayment_details WHERE member_id='%s';", mem_id);
				mysql_query(conn, std_qry);
				res = mysql_store_result(conn);
				while((row = mysql_fetch_row(res))){
					printf("Repayment_amount: %s\n", row[0]); //Print fetched loan status
					strcpy(msg, row[0]);
					strcat(msg, ". This is the amount your supposed to pay monthly.");
					send(accfd, msg, sizeof(msg), 0);
				}
			} else {
				//Append command to file
				FILE *fp;
				fp = fopen("sacco.txt","a");
				if(fp != NULL){
					fprintf(fp, "%s\n", buff);				
					fclose(fp);
				}else{
					printf("Failed to open file!");
				}
			}
	}
	
	//CLose connection to client and wait for new connections
	close(accfd);
	goto wait;
	
	return 0;
}
