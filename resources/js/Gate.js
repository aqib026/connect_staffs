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
    userName() {
        let name = '';
        if (this.user.name != null) name += this.user.name;
        if (this.user.last_name != null) name += ' ' + this.user.last_name;
        return name;
    }


}