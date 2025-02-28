<?php
/* @var Template $tpl */
/* @var Tools $tool */
?>
<div id="profile_print_area">
    <div class="body">
        <div class="row-fluid">
            <!-- BEGIN LOGO COMPANY -->
            <div class="span7 text-center">
                <img class="logo" src="<?php echo $tool->getWebUrl() ?>/img/logo2.png" alt="Iqra" title="Iqra" height="77" width="100">
            </div>

            <div class="span5">
                <dl class="dl-horizontal">
                    <dt><span class="fonts"><?php Tools::trans("Name & Father Name") ?></span></dt>
                    <dd>
                        <span class="fonts"><?php echo $name ?> <?php echo $tpl->getGenderTrans($gender) ?> <?php echo $fname ?></span>
                    </dd>
                    <dt><span class="fonts"><?php Tools::trans("Branch") ?></span></dt>
                    <dd>
                        <span class="fonts"><?php echo $student['branch_title'] ?></span>
                    </dd>
                    <dt><span class="fonts"><?php Tools::trans("Class") ?></span></dt>
                    <dd>
                        <span class="fonts"><?php echo $student['class_title'] ?></span>
                    </dd>
                    <dt><span class="fonts"><?php Tools::trans("Section") ?></span></dt>
                    <dd>
                        <span class="fonts"><?php echo $student['section_title'] ?></span>
                    </dd>

                </dl>
            </div>

        </div>


        <table class="table table-striped table-bordered table-condensed table-hover">


            <tr>
                <td colspan="6" class="fonts" style=" font-size: 18px; text-align: center">
                    <?php Tools::trans("Student Information") ?>

                </td>
            </tr>
            <tr>
                <td width="150" class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Gr Number") ?></td>
                <td width="150" class="fonts"><?php echo $grnumber; ?></td>
                <td width="150" class="fonts" style=" font-size: 18px; color: #002b80"><?php Tools::trans("Name") ?></td>
                <td width="150" class="fonts"><?php echo $name; ?></td>
                <td width="150" class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Father Name") ?></td>
                <td width="150" class="fonts"><?php echo $fname; ?></td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("B Form Number") ?></td>
                <td class="fonts"><?php echo $bform; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Blood Group") ?></td>
                <td class="fonts"><?php echo $bloud_group; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Home Phone") ?></td>
                <td class="fonts"><?php echo $home_fone; ?></td>
            </tr>

            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Current Address") ?></td>
                <td class="fonts"><?php echo $current_address; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Postal Code") ?></td>
                <td class="fonts"><?php echo $postcode; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Disability") ?></td>
                <td class="fonts"><?php echo $injury; ?></td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Emergency Contact Name") ?></td>
                <td class="fonts"><?php echo $amergency_name; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Emergency Phone") ?></td>
                <td class="fonts"><?php echo $amergency_contact; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Emergency Mobile") ?></td>
                <td class="fonts"><?php echo $amergency_mobile; ?></td>
            </tr>

            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Date of Birth") ?></td>
                <td class="fonts"><?php echo $date_of_birth; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80">&nbsp;</td>
                <td class="fonts"><?php //echo $eng_name; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80">&nbsp;</td>
                <td class="fonts"><?php //echo $eng_fname; ?></td>


            </tr>


            <tr>
                <td colspan="6" class="fonts" style=" font-size: 18px; text-align: center"><?php Tools::trans("Father Information") ?></td>
            </tr>

            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Name") ?></td>
                <td class="fonts"><?php echo $fname; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Father CNIC") ?></td>
                <td class="fonts"><?php echo $father_nic; ?></td>
                <td class="fonts" style="text-align:left">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Father Education") ?></td>
                <td class="fonts"><?php echo $father_education; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Father Mobile") ?></td>
                <td class="fonts"><?php echo $father_mobile; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Father Email") ?></td>
                <td class="fonts"><?php echo $father_email; ?></td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Father Information") ?></td>
                <td class="fonts"><?php echo $father_habits; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Father Job") ?></td>
                <td class="fonts"> <?php echo $father_occupation; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>


            <tr>
                <td colspan="6" class="fonts" style=" font-size: 18px; text-align: center"><?php Tools::trans("Mother Information") ?></td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Name") ?></td>
                <td class="fonts"><?php echo $mother_name; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Mother CNIC") ?></td>
                <td class="fonts"><?php echo $mother_nic; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Mother Education") ?></td>
                <td class="fonts"><?php echo $mother_education; ?></td>
            </tr>


            <tr>

                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Mother Mobile") ?></td>
                <td class="fonts"><?php echo $mother_mobile; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Mother Routine") ?></td>
                <td class="fonts"><?php echo $mother_habits; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>


            <tr>
                <td colspan="6" class="fonts" style=" font-size: 18px; text-align: center"><?php Tools::trans("Guardian Information") ?></td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Guardian Name") ?></td>
                <td><?php echo $gargin_name; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Guardian CNIC") ?></td>
                <td><?php echo $gargin_nic; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Guardian Qualification") ?></td>
                <td class="fonts"><?php echo $gargin_education; ?></td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Guardian Mobile") ?></td>
                <td class="fonts"><?php echo $gargin_mobile; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Guardian Routine") ?></td>
                <td class="fonts"><?php echo $gargin_mobile; ?></td>
                <td class="fonts" style="text-align:left">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>


            <tr>
                <td colspan="6" class="fonts" style=" font-size: 18px; text-align: center"><?php Tools::trans("Office Use") ?></td>
            </tr>

            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Test Numbers") ?></td>
                <td class="fonts"><?php echo $test_numbers; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Examine Opinion") ?></td>
                <td class="fonts"><?php echo $examin_opinion; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Conditional Admission") ?></td>
                <td class="fonts"><?php echo $term ?></td>
            </tr>


            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Instructions") ?></td>
                <td class="fonts"><?php echo $instruc; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Transportation") ?></td>
                <td class="fonts"><?php echo $transport ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("City") ?></td>
                <td class="fonts"><?php echo $city ?></td>
            </tr>

            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Street") ?></td>
                <td class="fonts"><?php echo $sreet; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Block") ?></td>
                <td class="fonts"><?php echo $block; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Final Approval") ?></td>
                <td class="fonts"><?php echo $approval; ?></td>
            </tr>

            <tr>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Author Name") ?></td>
                <td class="fonts"><?php echo $author; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Admission Date") ?></td>
                <td class="fonts"><?php echo $doa; ?></td>
                <td class="fonts" style=" font-size: 14px; color: #002b80"><?php Tools::trans("Gender") ?></td>
                <td class="fonts"><?php echo $tpl->getGenderTrans($gender) ?></td>
            </tr>


        </table>


    </div>
</div>
