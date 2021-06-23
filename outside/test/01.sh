#!/bin/bash


# read -p "键入两个数:" a b c
echo $0
echo $1
echo $2
echo "参数nums:$#"
echo "*"$*

for M in $*
do 
 echo $M
done
echo "@"$@

for M in $@
do 
 echo $M
done

echo "传递参数作为一个字符串显示:$*"
for M in "$*"
do
 echo $M
done

echo "传递参数分开显示:$@"
for N in "$@"
do
 echo $N
done
echo "PID$$"
$JAVA_HOME
echo $?
