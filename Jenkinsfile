String branchName = "master"
String gitCredentials = "phe-jenkins-ssh"
String repoUrl = "git@github.com:publichealthengland/foodscanner_compute.git"

pipeline
{
    options
    {
        buildDiscarder(logRotator(numToKeepStr: '3'))
    }
    agent any
    environment
    {
        VERSION = 'latest'
        PROJECT = 'swaps-cache-builder-stg'
        IMAGE = 'swaps-cache-builder-stg:latest'
//         DOCKERFILEPATH = "--file=docker/Dockerfile ."
        ECRURL = 'https://422072214762.dkr.ecr.eu-west-2.amazonaws.com/swaps-cache-builder-stg'
        ECRCRED = 'ecr:eu-west-2:phe-uat'
    }
    stages
    {
        stage('Git Checkout')
        {
            steps
            {
                script
                {
                    checkout([$class: 'GitSCM',
                        branches: [[name: branchName ]],
                        doGenerateSubmoduleConfigurations: false,
                        extensions: [],
                        submoduleCfg: [],
                        userRemoteConfigs: [[
                            url: repoUrl,
                            credentialsId: gitCredentials
                        ]]
                    ])
                }
            }
        }

        stage('Docker build')
        {
            steps
            {
                script
                {
                    // Build the docker image using a Dockerfile
                    docker.build("$IMAGE")
                }
            }
        }

        stage('Docker push')
        {
            steps
            {
                script
                {
                    // login to ECR - for now it seems that that the ECR Jenkins plugin is not performing the login as expected. I hope it will in the future.
                    sh("eval \$(aws ecr get-login --no-include-email | sed 's|https://||')")
                    // Push the Docker image to ECR
                    docker.withRegistry(ECRURL, ECRCRED)
                    {
                        docker.image(IMAGE).push()
                    }
                }
            }
        }
    }

    post
    {
        always
        {
            // make sure that the Docker image is removed
            sh "docker rmi $IMAGE | true"
        }
    }
}

