 #include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <netdb.h>
#include <stdio.h>
#include <arpa/inet.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>

int main(int argc, char *argv[]){
	
	//Declare variables
	int sockfd;
	struct sockaddr_in serv_addr;
	struct hostent *hp;
	char buff[1024];
	char log_out[16] = "logout";
	
	//Create socket
	sockfd = socket(AF_INET, SOCK_STREAM, 0);
	if(sockfd < 0){
		perror("Failed to create socket!");
		exit(1);
	}
	
	serv_addr.sin_family = AF_INET;
	hp = gethostbyname("localhost");
	
	if(hp == 0){
		perror("gethostbyname failed\n");
		close(sockfd);
		exit(1);
	}
	
	memcpy(&(serv_addr.sin_addr.s_addr), hp->h_addr, hp->h_length);
	serv_addr.sin_port = htons(5500);
	
	//Connect to server
	if(connect(sockfd, (struct sockaddr *)&serv_addr, sizeof(serv_addr))<0){
		perror("Error connecting to server!");
		exit(1);
	} else{
		puts("Connected to server!");
	}

	//Send username and password to server for authentication
	char login_cred[64];
	Repeat:
	puts("NOTE: Enter username and password like username password");
	printf("Enter username and password: ");
	fgets(login_cred, 64, stdin);
	send(sockfd, login_cred, sizeof(login_cred), 0);
	
	//Receive message on authentication
	char authentic[512];
	recv(sockfd, &authentic, sizeof(authentic), 0);
	printf("%s\n", authentic);

	char msg_authentic[64] = "Incorrect username or password\n\n";
	if(strcmp(msg_authentic, authentic) == 0){
		goto Repeat;
	}

	//Opening message
	puts("");
	puts("Welcome to the Family SACCO Management System.");
	puts("You may use the following commands: ");
	puts("\tcontribution receipt_number person_name amount date     -- To submit a 	contribution");
	puts("\tcontribution check --- to see how much has been contributed");
	puts("\tbenefits check ---- To see how much has been received in benefits only");
	puts("\tloan_request amount date_of_borrowing date_of_paying --- request for loan");
	puts("\tloan status  --- check loan status (Approved, denied or pending)");
	puts("\tload repayment_details – check the loan repayment details ie which amounts and how much per month");
	puts("\tidea name capital “simple description”");
	puts("\tloan_repayment amount date loan_number --- To pay monthly installment for loan");
	puts("NOTE: All dates are to be given in the format: yyyy-mm-dd");
	puts("");
	
	//Receive ID to append to commands
	char mem_id[11];
	recv(sockfd, &mem_id, sizeof(mem_id), 0);

	//Send commands
	char buff_msg[1024];

	char cmd_one[30] = "contribution check";
	char cmd_two[30] = "benefits check";
	char cmd_three[30] = "loan status"; 
	char cmd_four[30] = "load repayment_details";
	
	do{
		//Initialize character arrays
		//memset(buff_msg, 0, sizeof(buff));
		//memset(buff, 0, sizeof(buff));
		
		//Write message to send to server
		printf("Enter command > ");
		fgets(buff, 100, stdin);
	
		//Send data to server
			//For logout
			if(strncmp(log_out, buff, 6) == 0){
				send(sockfd, buff, sizeof(buff), 0);
				break;
			} else if((strncmp(buff, cmd_one, 18) == 0)||(strncmp(buff, cmd_two, 14) == 0)||(strncmp(buff, cmd_three, 11) == 0)||(strncmp(buff, cmd_four, 22) == 0)){
				send(sockfd, buff, sizeof(buff), 0);
				int rcv_arg = recv(sockfd, &buff_msg, sizeof(buff_msg), 0);
				
				if(rcv_arg < 0){
					perror("Reading stream message error!");
				} else if(rcv_arg == 0){
					printf("Ending connection...\n");
				} else {
					printf("MSG: %s\n", buff_msg);
				}
				printf("Message received, rcv_val = %d\n", rcv_arg);
				
				//printf("%s\n", buff_msg);	
			} else {
				buff[strlen(buff)-1] = '\0';
				strcat(buff, " ");
				strcat(buff, mem_id);
				send(sockfd, buff, sizeof(buff), 0);
				printf("Sent: %s\n", buff);
			}
		
	} while(1);
	
	return 0;
}
