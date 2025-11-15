pipeline {
    agent any

    environment {
        COMPOSE_PROJECT_NAME = 'gptdome-deployment'
        AWS_ACCESS_KEY_ID=credentials('AWS_ACCESS_KEY_ID')
        AWS_SECRET_ACCESS_KEY=credentials('AWS_SECRET_ACCESS_KEY')
        GPTDOME_SUPABASE_DB_URL=credentials('GPTDOME_SUPABASE_DB_URL')
        OPENAI_API_KEY=credentials('OPENAI_API_KEY')
        OPENAI_ORGANIZATION=credentials('OPENAI_ORGANIZATION')
    }

    stages {
        stage('Clean Previous') {
            steps {
                sh '''
                    # Remove containers
                    docker compose down
                    
                    # Remove project images
                    docker images -q ${COMPOSE_PROJECT_NAME}* | xargs -r docker rmi -f
                    
                    # Clean network
                    docker network rm gptdome-network || true
                '''
            }
        }

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build') {
            steps {
                sh 'docker compose build'
            }
        }

        stage('Deploy') {
            steps {
                withCredentials([
                string(credentialsId: 'AWS_ACCESS_KEY_ID', variable: 'AWS_ACCESS_KEY_ID'),
                string(credentialsId: 'AWS_SECRET_ACCESS_KEY', variable: 'AWS_SECRET_ACCESS_KEY'),
                string(credentialsId: 'GPTDOME_SUPABASE_DB_URL', variable: 'GPTDOME_SUPABASE_DB_URL'),
                string(credentialsId: 'OPENAI_API_KEY', variable: 'OPENAI_API_KEY'),
                string(credentialsId: 'OPENAI_ORGANIZATION', variable: 'OPENAI_ORGANIZATION')
      
                ]) 
                    {
                    sh """
                        docker compose up -d
                        
                        # Wait container ready
                        sleep 10
                        
                        # Run migration and setup Laravel
                        docker compose exec -T app cp .env.example .env
                        docker compose exec -T app php artisan key:generate

                        # Inject secrets
                        echo "AWS_ACCESS_KEY_ID=\$AWS_ACCESS_KEY_ID"
            
                        docker compose exec -T app sh -c 'sed -i "s|^AWS_ACCESS_KEY_ID=.*|AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}|" .env'
                        docker compose exec -T app sh -c 'sed -i "s|^AWS_SECRET_ACCESS_KEY=.*|AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}|" .env'
                        docker compose exec -T app sh -c 'sed -i "s|^DB_URL=.*|DB_URL=${GPTDOME_SUPABASE_DB_URL}|" .env'
                        docker compose exec -T app sh -c 'sed -i "s|^OPENAI_API_KEY=.*|OPENAI_API_KEY=${OPENAI_API_KEY}|" .env'
                        docker compose exec -T app sh -c 'sed -i "s|^OPENAI_ORGANIZATION=.*|OPENAI_ORGANIZATION=${OPENAI_ORGANIZATION}|" .env'
                                                
                        docker compose exec -T app php artisan optimize:clear
                        docker compose exec -T app php artisan optimize

                        docker compose exec -T app npm run build


                    """
                }
            }
        }
    }

    post {
        always {
            sh 'docker compose ps'
            cleanWs()
        }
    }
}