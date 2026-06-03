erDiagram

ACADEMIC_LEVEL ||--o{ ACADEMIC_GROUP : contains

PROGRAM ||--o{ ACADEMIC_GROUP : contains

ROOM_TYPE ||--o{ ROOM : categorizes

SCHEDULE_WEEK ||--o{ COURSE_SESSION : contains

TEACHER ||--o{ COURSE_SESSION : teaches

SUBJECT ||--o{ COURSE_SESSION : concerns

ROOM ||--o{ COURSE_SESSION : allocated

TIME_SLOT ||--o{ COURSE_SESSION : scheduled

COURSE_SESSION ||--o{ COURSE_SESSION_GROUP : includes

ACADEMIC_GROUP ||--o{ COURSE_SESSION_GROUP : attends