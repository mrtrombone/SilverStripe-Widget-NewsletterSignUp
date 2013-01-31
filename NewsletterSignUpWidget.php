<?php

class NewsletterSignUpWidget extends Widget 
{ 
   static $title = ""; 
   static $cmsTitle = "Newsletter Sign Up"; 
   static $description = "Newsletter Sign Up - requires page with URL segment 'thanks-for-joining' in the CMS"; 

   static $db = array(
        "ButtonText" => "Varchar"
   );

   static $defaults = array(
        "ButtonText" => "Sign Me Up!"
    );

   public function getCMSFields() {
      return new FieldList(
            new TextField("ButtonText", "Submit Button Text")
      );
   }

}

class NewsletterSignUpWidget_Controller extends Widget_Controller 
{ 
   function NewsletterSignUpForm() 
   {       
      return new Form($this, 
         'NewsletterSignUpForm',
         new FieldList(
               //TextField::create("FirstName"),
               EmailField::create("Email")->setAttribute('type', 'email')->setAttribute('placeholder', 'E-mail address')
         ),
         new FieldList(
               FormAction::create("doSignUpForm")->setTitle($this->ButtonText)               
         ),
         new RequiredFields( // validation
              //"FirstName",
              "Email"
          )
      ); 
   }

   function doSignUpForm($data) 
   { 
      $member = new Member(); 
      //$member->FirstName = $data['FirstName']; 
      $member->Email = $data['Email'];

      // Write it to the database. This needs to happen before we add it to a group 
      $member->write();

      //Find or create the 'Newsletter' group
        if(!$userGroup = DataObject::get_one('Group', "Code = 'Newsletter'"))
        {
            $userGroup = new Group();
            $userGroup->Code = "newsletter";
            $userGroup->Title = "Newsletter";
            $userGroup->Write();            
        }
        //Add member to user group
        $userGroup->Members()->add($member);

      // Redirect to a page thanking people for registering, this needs creating in the CMS 
      $this->redirect(Director::baseUrl().'thanks-for-joining'); 
   } 
}