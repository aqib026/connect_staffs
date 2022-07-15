export default class Gate {

    constructor(user) {
        this.user = user;
    }

    isAdmin() {
        return (this.user.type === 'admin' || this.user.type === 'superadmin');
    }

    isUser() {
        return (this.user.type === 'worker');
    }

    isTeacher() {
        return (this.user.type === 'teacher');
    }

    isStudent() {
        return (this.user.type === 'student');
    }

}

