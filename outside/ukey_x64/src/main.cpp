#include "../include/skfapi.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/time.h>


#define	TRUE	1
#define FALSE	0
#define ERROR_THROW(r) {if((r) != SAR_OK) goto END_OF_FUN;}


extern "C" ULONG  VertifyUserPinCode(ULONG type, char* pUserPin)
{
    ULONG ulRslt = SAR_OK;
    HANDLE hdev = NULL;
    HANDLE happ = NULL;
    HANDLE hkey = NULL;
    HANDLE hcont = NULL;
    char   szDevName[256] = {0};
    ULONG	ulDevNameLen = 256;
    char	szAppName[256] = {0};
    ULONG	ulAppNameLen = 256;
    ULONG	ulRetryCount = 0;
    char	szContName[256] = {0};
	ULONG	ulContName = sizeof(szContName);
    char cert[102400] = {0};
	ULONG ulCert = sizeof(cert);

    char *pdevname = szDevName;
    char *pappname = szAppName;
    char *pContName = szContName;
    char *pCert = cert;

    ULONG  ulPINType = type;
    ULONG ulMaxRetryCount = 0;
    ULONG ulRemainRetryCount = 0;
    BOOL bDefaultPin = 0;

    ulRslt = SKF_EnumDev(TRUE, szDevName, &ulDevNameLen);
    ERROR_THROW(ulRslt)

    ulRslt = SKF_ConnectDev(pdevname, &hdev);
    ERROR_THROW(ulRslt)

    ulRslt = SKF_EnumApplication(hdev, szAppName, &ulAppNameLen);
    ERROR_THROW(ulRslt)

    ulRslt = SKF_OpenApplication(hdev, pappname, &happ);
    ERROR_THROW(ulRslt)

    ulRslt = SKF_VerifyPIN(happ, type, pUserPin, &ulRetryCount);
    ERROR_THROW(ulRslt)
    
    
END_OF_FUN:
    if(hcont)
		SKF_CloseContainer(hcont);
    if(happ)
        SKF_CloseApplication(happ);
    if(hdev)
        SKF_DisConnectDev(hdev);

    return ulRslt;
}

extern "C" ULONG GetUsbKeyInfo(char *buf, int len)
{
    ULONG ulRslt = SAR_OK;
    HANDLE hdev = NULL;
    char   szDevName[256] = {0};
    ULONG	ulDevNameLen = 256;
    char *pdevname = szDevName;
    DEVINFO info = {0};
    memset(buf,0,sizeof(buf));

    ulDevNameLen = 256;
    pdevname = szDevName;
    ulRslt = SKF_EnumDev(TRUE, szDevName, &ulDevNameLen);
    if(ulRslt != SAR_OK)
    {
        printf("Enum device error.%08x\n", ulRslt);
        goto END_OF_FUN;
    }

    if(strlen(pdevname) == 0)
    {
        printf("\tNot found device. \n");
        goto END_OF_FUN;
    }

    ulRslt = SKF_ConnectDev(pdevname, &hdev);
    if(ulRslt != SAR_OK)
    {
        printf("Connect device error.%08x\n", ulRslt);
        goto END_OF_FUN;
    }
    memset(&info, 0, sizeof(info));
    ulRslt = SKF_GetDevInfo(hdev, &info);
    if(ulRslt != SAR_OK)
    {
        printf("Get device infomation error.%08x\n", ulRslt);
        goto END_OF_FUN;
    }

    SKF_DisConnectDev(hdev);
    strcat(buf, info.SerialNumber);
    pdevname += strlen(pdevname) + 1;

END_OF_FUN:
    if (hdev)
        SKF_DisConnectDev(hdev);
    return -1;
}

extern "C" ULONG GetUsbManufacturerInfo(char *buf, int len)
{
    ULONG ulRslt = SAR_OK;
    HANDLE hdev = NULL;
    char   szDevName[256] = {0};
    ULONG	ulDevNameLen = 256;
    char *pdevname = szDevName;
    DEVINFO info = {0};
    memset(buf,0,sizeof(buf));

    ulDevNameLen = 256;
    pdevname = szDevName;
    ulRslt = SKF_EnumDev(TRUE, szDevName, &ulDevNameLen);
    if(ulRslt != SAR_OK)
    {
        printf("Enum device error.%08x\n", ulRslt);
        goto END_OF_FUN;
    }

    if(strlen(pdevname) == 0)
    {
        printf("\tNot found device. \n");
        goto END_OF_FUN;
    }

    ulRslt = SKF_ConnectDev(pdevname, &hdev);
    if(ulRslt != SAR_OK)
    {
        printf("Connect device error.%08x\n", ulRslt);
        goto END_OF_FUN;
    }
    memset(&info, 0, sizeof(info));
    ulRslt = SKF_GetDevInfo(hdev, &info);
    if(ulRslt != SAR_OK)
    {
        printf("Get device infomation error.%08x\n", ulRslt);
        goto END_OF_FUN;
    }
    
    SKF_DisConnectDev(hdev);
    strcat(buf, info.Manufacturer);
    pdevname += strlen(pdevname) + 1;

END_OF_FUN:
    if (hdev)
        SKF_DisConnectDev(hdev);
    return -1;
}

int main()
{
    char ppp[128] = {0};
    GetUsbKeyInfo(ppp, 128);
    printf("\n%s", ppp);

    char	*pUserPin = "123456";
    unsigned long ret = VertifyUserPinCode(1, pUserPin);
    printf("\n%lu\n", ret);
}

