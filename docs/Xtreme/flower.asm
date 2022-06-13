PUSH    CS                                 
POP     ES                                 
MOV     DI,8080h
MOV     CX,007Fh                            
line:
STOSB                                      
ADD     DI,00FFh                            
LOOP    line
MOV     CX,0060h                            
SUB     DI,CX                              
REPZ                                       
STOSW                                      
MOV     BX,4080h                            
MOV     CX,0050h                            
cloop:
PUSH    BX                                 
PUSH    CX                                 
CALL    Circ                               
POP     CX                                 
DEC     CX                                 
POP     BX                                 
ADD     BX,0100h                            
LOOP    cloop
HLT         
Circ:                               
MOV     [s],BX                          
SUB     BX,CX                              
ADD     CX,CX                              
MOV     DX,CX                              
ADD     DX,00FFh                            
XOR     AX,AX                              
loopc:
CALL    d2
CALL    d2                               
CMP     AL,DL                              
JL      down                               
INC     BL                                 
SUB     AL,DL                              
SUB     DL,02                              
JL      sof 
down:                              
CMP     AL,DL                              
JGE     l
INC     BH                                 
ADD     AL,DH                              
ADD     DH,02                              
l:
LOOP    loopc
sof:
RET                                        
s:
DB  'CG'
d2:
CALL    d1
MOV     [BX],BL                            
PUSH    AX                                 
MOV     AX,[s]                          
ADD     AX,AX                              
SUB     AX,BX                              
MOV     BL,AL                              
POP     AX                                 
RET                                        
d1:
MOV     [BX],BL                            
PUSH    AX                                 
MOV     AX,[s]                          
ADD     AH,AH                              
SUB     AH,BH                              
MOV     BH,AH                              
POP     AX                                 
RET                                        
d:
MOV     [BX],BL                            
SUB     BL,[s]                          
SUB     BH,[s+1]                          
XCHG    BL,BH                              
ADD     BL,[s]                          
ADD     BH,[s+1]                          
RET
