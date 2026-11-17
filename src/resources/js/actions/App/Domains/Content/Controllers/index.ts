import BlogPostController from './BlogPostController'
import ContactMessageController from './ContactMessageController'
import SuggestionController from './SuggestionController'
import ChatEscalationController from './ChatEscalationController'

const Controllers = {
    BlogPostController: Object.assign(BlogPostController, BlogPostController),
    ContactMessageController: Object.assign(ContactMessageController, ContactMessageController),
    SuggestionController: Object.assign(SuggestionController, SuggestionController),
    ChatEscalationController: Object.assign(ChatEscalationController, ChatEscalationController),
}

export default Controllers