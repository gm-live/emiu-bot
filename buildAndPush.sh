docker build --platform=linux/amd64 -t=wright1992/emiubot:amd64 .
docker push wright1992/emiubot:amd64
docker build --platform=linux/s390x -t=wright1992/emiubot:s390x .
docker push wright1992/emiubot:s390x
docker manifest rm wright1992/emiubot
docker manifest create wright1992/emiubot wright1992/emiubot:amd64 wright1992/emiubot:s390x
docker manifest push wright1992/emiubot


